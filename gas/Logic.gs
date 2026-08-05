/**
 * File Business Logic / Backend - Finud App
 * Seluruh fungsi logika dibungkus dalam konstanta LOGIC
 */

const LOGIC = {
  /**
   * Mengambil instance Spreadsheet aktif tempat script ini terpasang.
   * Mendukung skenario bound script (terhubung langsung) dan standalone script.
   */
  getSpreadsheet: function() {
    let ss = SpreadsheetApp.getActiveSpreadsheet();
    if (!ss) {
      const ssId = PropertiesService.getScriptProperties().getProperty('SPREADSHEET_ID');
      if (ssId) {
        try {
          ss = SpreadsheetApp.openById(ssId);
        } catch (e) {
          Logger.log("Gagal membuka Spreadsheet via ID: " + e.message);
        }
      }
    }
    if (!ss) {
      throw new Error("Spreadsheet tidak ditemukan. Pastikan Script ini dibuat melalui Google Sheets: Ekstensi > Apps Script.");
    }
    return ss;
  },

  /**
   * Menyiapkan seluruh sheet default ("Transaksi", "Detail_Transaksi", "Anggaran", "Rekening")
   * dan memperbaiki header kolom jika diperlukan.
   */
  initSheets: function() {
    const ss = this.getSpreadsheet();
    
    // 1. Sheet Rekening
    let sheetRekening = ss.getSheetByName("Rekening");
    if (!sheetRekening) {
      sheetRekening = ss.insertSheet("Rekening");
      sheetRekening.appendRow(["ID Rekening", "Nama Rekening", "Jenis Rekening", "Saldo Awal"]);
      sheetRekening.getRange("A1:D1").setFontWeight("bold");
      sheetRekening.appendRow(["REK-1", "Dompet Tunai", "Tunai", 500000]);
      sheetRekening.appendRow(["REK-2", "BCA Utama", "Bank", 5000000]);
      sheetRekening.appendRow(["REK-3", "GoPay", "E-Wallet", 250000]);
    }

    // 2. Sheet Anggaran
    let sheetAnggaran = ss.getSheetByName("Anggaran");
    if (!sheetAnggaran) {
      sheetAnggaran = ss.insertSheet("Anggaran");
      sheetAnggaran.appendRow(["Kategori", "Limit Anggaran"]);
      sheetAnggaran.getRange("A1:B1").setFontWeight("bold");
      sheetAnggaran.appendRow(["Makanan & Minuman", 1500000]);
      sheetAnggaran.appendRow(["Transportasi", 500000]);
      sheetAnggaran.appendRow(["Hiburan", 300000]);
      sheetAnggaran.appendRow(["Belanja", 800000]);
      sheetAnggaran.appendRow(["Tagihan & Utilitas", 1000000]);
    }

    // 3. Sheet Detail Transaksi (Rincian Struk Kasir)
    let sheetDetail = ss.getSheetByName("Detail_Transaksi");
    if (!sheetDetail) {
      sheetDetail = ss.insertSheet("Detail_Transaksi");
      sheetDetail.appendRow(["ID Transaksi", "ID Detail", "Nama Barang", "Jumlah", "Harga Satuan", "Diskon", "Subtotal"]);
      sheetDetail.getRange("A1:G1").setFontWeight("bold");
    }

    // 4. Sheet Transaksi (8 Kolom)
    let sheetTransaksi = ss.getSheetByName("Transaksi");
    if (!sheetTransaksi) {
      sheetTransaksi = ss.insertSheet("Transaksi");
      sheetTransaksi.appendRow(["ID Transaksi", "Tanggal", "Jenis", "Kategori", "Nominal", "Keterangan", "Rekening", "Rekening Tujuan"]);
      sheetTransaksi.getRange("A1:H1").setFontWeight("bold");
    } else {
      const lastCol = sheetTransaksi.getLastColumn();
      const lastRow = sheetTransaksi.getLastRow();
      
      const currentHeader = lastRow >= 1 ? sheetTransaksi.getRange(1, 1, 1, Math.max(lastCol, 8)).getValues()[0] : [];
      const col3Name = String(currentHeader[2] || '').trim();

      if (col3Name.toLowerCase() === 'kategori' || lastCol < 8) {
        if (lastRow >= 2) {
          const oldValues = sheetTransaksi.getRange(2, 1, lastRow - 1, Math.max(lastCol, 5)).getValues();
          const newRows = [];

          oldValues.forEach(row => {
            const id = row[0];
            const tgl = row[1];
            let jenis = 'Pengeluaran';
            let kat = '';
            let nom = 0;
            let ket = '';
            let rekAsal = 'Dompet Tunai';
            let rekTujuan = '';

            if (String(row[2]).trim() === 'Pemasukan' || String(row[2]).trim() === 'Pengeluaran' || String(row[2]).trim() === 'Transfer / Mutasi') {
              jenis = String(row[2]).trim();
              kat = String(row[3] || 'Umum').trim();
              nom = Number(row[4]) || 0;
              ket = String(row[5] || '').trim();
              rekAsal = String(row[6] || 'Dompet Tunai').trim();
              rekTujuan = String(row[7] || '').trim();
            } else {
              jenis = 'Pengeluaran';
              kat = String(row[2] || 'Umum').trim();
              nom = Number(row[3]) || 0;
              ket = String(row[4] || '').trim();
              rekAsal = String(row[5] || 'Dompet Tunai').trim();
            }

            newRows.push([id, tgl, jenis, kat, nom, ket, rekAsal, rekTujuan]);
          });

          sheetTransaksi.clear();
          sheetTransaksi.getRange("A1:H1").setValues([["ID Transaksi", "Tanggal", "Jenis", "Kategori", "Nominal", "Keterangan", "Rekening", "Rekening Tujuan"]]).setFontWeight("bold");
          if (newRows.length > 0) {
            sheetTransaksi.getRange(2, 1, newRows.length, 8).setValues(newRows);
          }
        } else {
          sheetTransaksi.getRange("A1:H1").setValues([["ID Transaksi", "Tanggal", "Jenis", "Kategori", "Nominal", "Keterangan", "Rekening", "Rekening Tujuan"]]).setFontWeight("bold");
        }
      }
    }

    return ss;
  },

  /**
   * Membantu validasi dan konversi nilai Tanggal dari Google Sheets.
   */
  parseDate: function(dateVal) {
    if (!dateVal) return null;
    if (dateVal instanceof Date) return isNaN(dateVal.getTime()) ? null : dateVal;
    if (typeof dateVal === 'string') {
      const str = dateVal.trim();
      if (!str) return null;
      let parsed = new Date(str);
      if (!isNaN(parsed.getTime())) return parsed;
      const parts = str.split(/[\/\-]/);
      if (parts.length === 3) {
        let day, month, year;
        if (parts[0].length === 4) {
          year = parseInt(parts[0], 10);
          month = parseInt(parts[1], 10) - 1;
          day = parseInt(parts[2], 10);
        } else {
          day = parseInt(parts[0], 10);
          month = parseInt(parts[1], 10) - 1;
          year = parseInt(parts[2], 10);
        }
        if (!isNaN(day) && !isNaN(month) && !isNaN(year)) {
          parsed = new Date(year, month, day);
          if (!isNaN(parsed.getTime())) return parsed;
        }
      }
    }
    if (typeof dateVal === 'number' && dateVal > 0) {
      const sheetEpoch = new Date(1899, 11, 30);
      const parsed = new Date(sheetEpoch.getTime() + dateVal * 86400000);
      if (!isNaN(parsed.getTime())) return parsed;
    }
    return null;
  },

  getDaftarKategori: function() {
    const ss = this.initSheets();
    const sheet = ss.getSheetByName("Anggaran");
    if (!sheet) return [];
    const lastRow = sheet.getLastRow();
    if (lastRow < 2) return [];

    const values = sheet.getRange(2, 1, lastRow - 1, 1).getValues();
    const categories = [];
    values.forEach(function(row) {
      const item = row[0];
      if (item !== null && item !== undefined) {
        const strVal = String(item).trim();
        if (strVal !== '' && !categories.includes(strVal)) {
          categories.push(strVal);
        }
      }
    });
    return categories;
  },

  getDaftarRekening: function() {
    const ss = this.initSheets();
    const sheetRekening = ss.getSheetByName("Rekening");

    const lastRowRek = sheetRekening.getLastRow();
    if (lastRowRek < 2) return [];

    const dataRek = sheetRekening.getRange(2, 1, lastRowRek - 1, 4).getValues();

    const sheetTrx = ss.getSheetByName("Transaksi");
    const mutasiMap = {};

    if (sheetTrx && sheetTrx.getLastRow() >= 2) {
      const lastRowTrx = sheetTrx.getLastRow();
      const numCols = Math.max(sheetTrx.getLastColumn(), 8);
      const dataTrx = sheetTrx.getRange(2, 1, lastRowTrx - 1, numCols).getValues();

      dataTrx.forEach(row => {
        let jenis = 'Pengeluaran';
        let nominal = 0;
        let rekAsal = '';
        let rekTujuan = '';

        if (String(row[2]).trim() === 'Pemasukan' || String(row[2]).trim() === 'Pengeluaran' || String(row[2]).trim() === 'Transfer / Mutasi') {
          jenis = String(row[2]).trim();
          nominal = Number(row[4]) || 0;
          rekAsal = String(row[6] || '').trim();
          rekTujuan = String(row[7] || '').trim();
        } else {
          jenis = 'Pengeluaran';
          nominal = Number(row[3]) || 0;
          rekAsal = String(row[5] || '').trim();
        }

        if (jenis === 'Pemasukan') {
          if (rekAsal) mutasiMap[rekAsal] = (mutasiMap[rekAsal] || 0) + nominal;
        } else if (jenis === 'Pengeluaran') {
          if (rekAsal) mutasiMap[rekAsal] = (mutasiMap[rekAsal] || 0) - nominal;
        } else if (jenis === 'Transfer / Mutasi') {
          if (rekAsal) mutasiMap[rekAsal] = (mutasiMap[rekAsal] || 0) - nominal;
          if (rekTujuan) mutasiMap[rekTujuan] = (mutasiMap[rekTujuan] || 0) + nominal;
        }
      });
    }

    const listRekening = [];
    dataRek.forEach(row => {
      const id = String(row[0] || '').trim();
      const nama = String(row[1] || '').trim();
      const jenis = String(row[2] || 'Bank').trim();
      const saldoAwal = Number(row[3]) || 0;

      if (id && nama) {
        const mutasi = (mutasiMap[nama] || 0) + (mutasiMap[id] || 0);
        const totalSaldo = saldoAwal + mutasi;

        listRekening.push({
          id: id,
          nama: nama,
          jenis: jenis,
          saldoAwal: saldoAwal,
          totalSaldo: totalSaldo
        });
      }
    });

    return listRekening;
  },

  getDetailRekening: function(params) {
    const idRekening = typeof params === 'object' ? params.id : params;
    const daftar = this.getDaftarRekening();
    const rek = daftar.find(r => r.id === idRekening || r.nama === idRekening);

    if (!rek) throw new Error("Rekening tidak ditemukan.");

    const ss = this.initSheets();
    const sheetTrx = ss.getSheetByName("Transaksi");
    const last3Transactions = [];

    if (sheetTrx && sheetTrx.getLastRow() >= 2) {
      const lastRowTrx = sheetTrx.getLastRow();
      const numCols = Math.max(sheetTrx.getLastColumn(), 8);
      const dataTrx = sheetTrx.getRange(2, 1, lastRowTrx - 1, numCols).getValues();

      const matchedTrx = [];

      for (let i = 0; i < dataTrx.length; i++) {
        const row = dataTrx[i];
        const id = String(row[0] || '');
        const rawDate = row[1];
        let jenis = 'Pengeluaran';
        let kategori = '';
        let nominal = 0;
        let keterangan = '';
        let rekAsal = '';
        let rekTujuan = '';

        if (String(row[2]).trim() === 'Pemasukan' || String(row[2]).trim() === 'Pengeluaran' || String(row[2]).trim() === 'Transfer / Mutasi') {
          jenis = String(row[2]).trim();
          kategori = String(row[3] || '').trim();
          nominal = Number(row[4]) || 0;
          keterangan = String(row[5] || '').trim();
          rekAsal = String(row[6] || '').trim();
          rekTujuan = String(row[7] || '').trim();
        } else {
          jenis = 'Pengeluaran';
          kategori = String(row[2] || '').trim();
          nominal = Number(row[3]) || 0;
          keterangan = String(row[4] || '').trim();
          rekAsal = String(row[5] || '').trim();
        }

        if (rekAsal === rek.nama || rekAsal === rek.id || rekTujuan === rek.nama || rekTujuan === rek.id) {
          const parsedDate = this.parseDate(rawDate);
          const formattedDate = parsedDate ? parsedDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) : '-';
          
          let adjustedJenis = jenis;
          if (jenis === 'Transfer / Mutasi') {
            adjustedJenis = (rekAsal === rek.nama || rekAsal === rek.id) ? 'Transfer Keluar' : 'Transfer Masuk';
          }

          matchedTrx.push({
            id: id,
            tanggal: formattedDate,
            rawDate: parsedDate ? parsedDate.getTime() : 0,
            jenis: adjustedJenis,
            kategori: kategori,
            nominal: nominal,
            keterangan: keterangan,
            rekening: rekAsal,
            rekeningTujuan: rekTujuan
          });
        }
      }

      matchedTrx.sort((a, b) => b.rawDate - a.rawDate);
      last3Transactions.push(...matchedTrx.slice(0, 3));
    }

    return {
      rekening: rek,
      recentTransactions: last3Transactions
    };
  },

  tambahRekening: function(params) {
    if (!params || !params.nama || !params.jenis) {
      throw new Error("Mohon isi Nama Rekening dan Jenis Rekening.");
    }

    const nama = String(params.nama).trim();
    const jenis = String(params.jenis).trim();
    const saldoAwal = Number(params.saldoAwal) || 0;

    const ss = this.initSheets();
    const sheet = ss.getSheetByName("Rekening");

    const lastRow = sheet.getLastRow();
    if (lastRow >= 2) {
      const existing = sheet.getRange(2, 2, lastRow - 1, 1).getValues();
      for (let i = 0; i < existing.length; i++) {
        if (String(existing[i][0]).trim().toLowerCase() === nama.toLowerCase()) {
          throw new Error('Rekening dengan nama "' + nama + '" sudah ada.');
        }
      }
    }

    const idUnik = "REK-" + Date.now();
    sheet.appendRow([idUnik, nama, jenis, saldoAwal]);

    return {
      success: true,
      message: 'Rekening "' + nama + '" berhasil ditambahkan!',
      id: idUnik
    };
  },

  editRekening: function(params) {
    if (!params || !params.id || !params.nama || !params.jenis) {
      throw new Error("Data rekening tidak lengkap.");
    }

    const id = String(params.id).trim();
    const namaBaru = String(params.nama).trim();
    const jenisBaru = String(params.jenis).trim();
    const saldoAwalBaru = Number(params.saldoAwal) || 0;

    const ss = this.initSheets();
    const sheet = ss.getSheetByName("Rekening");

    const lastRow = sheet.getLastRow();
    if (lastRow < 2) throw new Error("Data rekening tidak ditemukan.");

    const data = sheet.getRange(2, 1, lastRow - 1, 4).getValues();
    let rowIndexToEdit = -1;

    for (let i = 0; i < data.length; i++) {
      if (String(data[i][0]).trim() === id) {
        rowIndexToEdit = i + 2;
        break;
      }
    }

    if (rowIndexToEdit === -1) throw new Error("Rekening tidak ditemukan di spreadsheet.");

    sheet.getRange(rowIndexToEdit, 2).setValue(namaBaru);
    sheet.getRange(rowIndexToEdit, 3).setValue(jenisBaru);
    sheet.getRange(rowIndexToEdit, 4).setValue(saldoAwalBaru);

    return {
      success: true,
      message: 'Rekening "' + namaBaru + '" berhasil diperbarui!'
    };
  },

  hapusRekening: function(params) {
    const id = typeof params === 'object' ? params.id : params;
    if (!id) throw new Error("ID Rekening tidak valid.");

    const ss = this.initSheets();
    const sheet = ss.getSheetByName("Rekening");

    const lastRow = sheet.getLastRow();
    if (lastRow < 2) throw new Error("Data rekening tidak ditemukan.");

    const data = sheet.getRange(2, 1, lastRow - 1, 1).getValues();
    let rowIndexToDelete = -1;

    for (let i = 0; i < data.length; i++) {
      if (String(data[i][0]).trim() === String(id).trim()) {
        rowIndexToDelete = i + 2;
        break;
      }
    }

    if (rowIndexToDelete === -1) throw new Error("Rekening tidak ditemukan.");

    sheet.deleteRow(rowIndexToDelete);

    return {
      success: true,
      message: "Rekening berhasil dihapus!"
    };
  },

  getDashboardData: function() {
    const ss = this.initSheets();
    const sheetTransaksi = ss.getSheetByName("Transaksi");
    const sheetAnggaran = ss.getSheetByName("Anggaran");

    const now = new Date();
    const targetMonth = now.getMonth();
    const targetYear = now.getFullYear();

    const dateOptions = { day: 'numeric', month: 'long', year: 'numeric' };
    let todayFormatted = "";
    try {
      todayFormatted = now.toLocaleDateString('id-ID', dateOptions);
    } catch (e) {
      todayFormatted = now.toDateString();
    }

    let totalIncomeToday = 0;
    let totalExpenseToday = 0;

    const categorySpendingMonth = {};
    const monthActivityMap = {};
    const allTransactionsList = [];

    if (sheetTransaksi && sheetTransaksi.getLastRow() >= 2) {
      const lastRow = sheetTransaksi.getLastRow();
      const numCols = Math.max(sheetTransaksi.getLastColumn(), 8);
      const values = sheetTransaksi.getRange(2, 1, lastRow - 1, numCols).getValues();

      for (let i = 0; i < values.length; i++) {
        const row = values[i];
        const id = String(row[0] || '');
        const rawDate = row[1];
        
        let jenis = 'Pengeluaran';
        let kategori = '';
        let nominal = 0;
        let keterangan = '';
        let rekeningAsal = '';
        let rekeningTujuan = '';

        if (String(row[2]).trim() === 'Pemasukan' || String(row[2]).trim() === 'Pengeluaran' || String(row[2]).trim() === 'Transfer / Mutasi') {
          jenis = String(row[2]).trim();
          kategori = String(row[3] || '').trim();
          nominal = Number(row[4]) || 0;
          keterangan = String(row[5] || '').trim();
          rekeningAsal = String(row[6] || '').trim();
          rekeningTujuan = String(row[7] || '').trim();
        } else {
          jenis = 'Pengeluaran';
          kategori = String(row[2] || '').trim();
          nominal = Number(row[3]) || 0;
          keterangan = String(row[4] || '').trim();
          rekeningAsal = String(row[5] || '').trim();
        }

        const parsedDate = this.parseDate(rawDate);

        if (parsedDate) {
          if (parsedDate.getDate() === now.getDate() &&
              parsedDate.getMonth() === targetMonth &&
              parsedDate.getFullYear() === targetYear) {
            if (jenis === 'Pemasukan') {
              totalIncomeToday += nominal;
            } else if (jenis === 'Pengeluaran') {
              totalExpenseToday += nominal;
            }
          }

          if (parsedDate.getMonth() === targetMonth && parsedDate.getFullYear() === targetYear) {
            const dayNum = parsedDate.getDate();
            if (!monthActivityMap[dayNum]) {
              monthActivityMap[dayNum] = { income: 0, expense: 0 };
            }
            if (jenis === 'Pemasukan') {
              monthActivityMap[dayNum].income += nominal;
            } else if (jenis === 'Pengeluaran') {
              monthActivityMap[dayNum].expense += nominal;
              if (kategori) {
                categorySpendingMonth[kategori] = (categorySpendingMonth[kategori] || 0) + nominal;
              }
            }
          }

          let formattedTrxDate = "";
          try {
            formattedTrxDate = parsedDate.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
          } catch (e) {
            formattedTrxDate = parsedDate.toDateString();
          }

          allTransactionsList.push({
            id: id || ('TRX-' + i),
            tanggal: formattedTrxDate,
            rawDate: parsedDate.getTime(),
            jenis: jenis,
            kategori: kategori || (jenis === 'Transfer / Mutasi' ? 'Transfer' : 'Umum'),
            nominal: nominal,
            keterangan: keterangan,
            rekening: rekeningAsal,
            rekeningTujuan: rekeningTujuan
          });
        }
      }
    }

    allTransactionsList.sort((a, b) => b.rawDate - a.rawDate);
    const recentTransactions = allTransactionsList.slice(0, 5);

    const daftarRekening = this.getDaftarRekening();
    let totalSaldoAllTime = 0;
    daftarRekening.forEach(r => { totalSaldoAllTime += r.totalSaldo; });

    let totalBudgetLimit = 0;
    let totalBudgetSpent = 0;
    const categoryBudgetList = [];

    if (sheetAnggaran && sheetAnggaran.getLastRow() >= 2) {
      const lastRow = sheetAnggaran.getLastRow();
      const valuesAnggaran = sheetAnggaran.getRange(2, 1, lastRow - 1, 2).getValues();

      valuesAnggaran.forEach(row => {
        const catName = String(row[0] || '').trim();
        const limit = Number(row[1]) || 0;

        if (catName !== '') {
          const spent = categorySpendingMonth[catName] || 0;
          totalBudgetLimit += limit;
          totalBudgetSpent += spent;
          const percentage = limit > 0 ? Math.round((spent / limit) * 100) : 0;

          categoryBudgetList.push({
            kategori: catName,
            limit: limit,
            spent: spent,
            remaining: limit - spent,
            percentage: percentage
          });
        }
      });
    }

    const topExpenses = Object.keys(categorySpendingMonth)
      .map(cat => ({
        kategori: cat,
        total: categorySpendingMonth[cat]
      }))
      .sort((a, b) => b.total - a.total)
      .slice(0, 5);

    const overallBudgetPercentage = totalBudgetLimit > 0 ? Math.round((totalBudgetSpent / totalBudgetLimit) * 100) : 0;

    return {
      todayStats: {
        dateStr: todayFormatted,
        totalSaldo: totalSaldoAllTime,
        pemasukanHariIni: totalIncomeToday,
        pengeluaranHariIni: totalExpenseToday
      },
      budgetRealization: {
        totalLimit: totalBudgetLimit,
        totalSpent: totalBudgetSpent,
        totalSisa: totalBudgetLimit - totalBudgetSpent,
        percentage: overallBudgetPercentage,
        categoriesList: categoryBudgetList
      },
      topExpenses: topExpenses,
      monthActivity: monthActivityMap,
      recentTransactions: recentTransactions,
      categories: this.getDaftarKategori(),
      rekeningList: daftarRekening
    };
  },

  /**
   * Mengambil Seluruh Riwayat Transaksi dikelompokkan per Tanggal untuk Halaman Transaksi.
   * Termasuk melampirkan rincian barang belanjaan (kasir) dari sheet "Detail_Transaksi".
   */
  getRiwayatTransaksiGrouped: function() {
    const ss = this.initSheets();
    const sheetTrx = ss.getSheetByName("Transaksi");
    const sheetDetail = ss.getSheetByName("Detail_Transaksi");

    // Map rincian barang per ID Transaksi
    const detailMap = {};
    if (sheetDetail && sheetDetail.getLastRow() >= 2) {
      const detailValues = sheetDetail.getRange(2, 1, sheetDetail.getLastRow() - 1, 7).getValues();
      detailValues.forEach(row => {
        const idTrx = String(row[0] || '').trim();
        if (idTrx) {
          if (!detailMap[idTrx]) detailMap[idTrx] = [];
          detailMap[idTrx].push({
            idDetail: String(row[1] || ''),
            nama: String(row[2] || ''),
            jumlah: Number(row[3]) || 1,
            hargaSatuan: Number(row[4]) || 0,
            diskon: Number(row[5]) || 0,
            subtotal: Number(row[6]) || 0
          });
        }
      });
    }

    const groupedMap = {}; // { dateKey: { dateStr, totalIncome, totalExpense, items: [] } }
    const dateKeysOrdered = [];

    if (sheetTrx && sheetTrx.getLastRow() >= 2) {
      const lastRow = sheetTrx.getLastRow();
      const numCols = Math.max(sheetTrx.getLastColumn(), 8);
      const values = sheetTrx.getRange(2, 1, lastRow - 1, numCols).getValues();

      for (let i = 0; i < values.length; i++) {
        const row = values[i];
        const id = String(row[0] || '').trim();
        const rawDate = row[1];
        
        let jenis = 'Pengeluaran';
        let kategori = '';
        let nominal = 0;
        let keterangan = '';
        let rekeningAsal = '';
        let rekeningTujuan = '';

        if (String(row[2]).trim() === 'Pemasukan' || String(row[2]).trim() === 'Pengeluaran' || String(row[2]).trim() === 'Transfer / Mutasi') {
          jenis = String(row[2]).trim();
          kategori = String(row[3] || '').trim();
          nominal = Number(row[4]) || 0;
          keterangan = String(row[5] || '').trim();
          rekeningAsal = String(row[6] || '').trim();
          rekeningTujuan = String(row[7] || '').trim();
        } else {
          jenis = 'Pengeluaran';
          kategori = String(row[2] || '').trim();
          nominal = Number(row[3]) || 0;
          keterangan = String(row[4] || '').trim();
          rekeningAsal = String(row[5] || '').trim();
        }

        const parsedDate = this.parseDate(rawDate);
        if (parsedDate) {
          const yyyy = parsedDate.getFullYear();
          const mm = String(parsedDate.getMonth() + 1).padStart(2, '0');
          const dd = String(parsedDate.getDate()).padStart(2, '0');
          const dateKey = `${yyyy}-${mm}-${dd}`;

          let dateStr = "";
          try {
            dateStr = parsedDate.toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
          } catch (e) {
            dateStr = dateKey;
          }

          if (!groupedMap[dateKey]) {
            groupedMap[dateKey] = {
              dateKey: dateKey,
              dateStr: dateStr,
              timestamp: parsedDate.getTime(),
              totalIncome: 0,
              totalExpense: 0,
              items: []
            };
            dateKeysOrdered.push(dateKey);
          }

          if (jenis === 'Pemasukan') {
            groupedMap[dateKey].totalIncome += nominal;
          } else if (jenis === 'Pengeluaran') {
            groupedMap[dateKey].totalExpense += nominal;
          }

          groupedMap[dateKey].items.push({
            id: id,
            tanggal: dateKey,
            rawDateStr: dateStr,
            jenis: jenis,
            kategori: kategori || (jenis === 'Transfer / Mutasi' ? 'Transfer' : 'Umum'),
            nominal: nominal,
            keterangan: keterangan,
            rekening: rekeningAsal,
            rekeningTujuan: rekeningTujuan,
            itemsDetail: detailMap[id] || []
          });
        }
      }
    }

    // Urutkan grup dari tanggal terbaru ke terlama
    dateKeysOrdered.sort((a, b) => groupedMap[b].timestamp - groupedMap[a].timestamp);

    const resultGrouped = dateKeysOrdered.map(key => groupedMap[key]);
    return resultGrouped;
  },

  simpanTransaksi: function(params) {
    if (!params || !params.tanggal || !params.nominal) {
      throw new Error("Mohon isi Tanggal dan Nominal transaksi.");
    }

    const jenis = String(params.jenis || 'Pengeluaran').trim();
    
    if (jenis === 'Transfer / Mutasi') {
      if (!params.rekening || !params.rekeningTujuan) {
        throw new Error("Mohon pilih Rekening Asal dan Rekening Tujuan transfer.");
      }
      if (params.rekening === params.rekeningTujuan) {
        throw new Error("Rekening Tujuan tidak boleh sama dengan Rekening Asal.");
      }
    }

    const ss = this.initSheets();
    const sheetTransaksi = ss.getSheetByName("Transaksi");

    const idUnik = "TRX-" + Date.now();
    const tanggal = params.tanggal;
    const kategori = String(params.kategori || (jenis === 'Transfer / Mutasi' ? 'Transfer' : 'Umum')).trim();
    const nominal = Number(params.nominal) || 0;
    const keterangan = String(params.keterangan || '').trim();
    const rekeningAsal = String(params.rekening || 'Dompet Tunai').trim();
    const rekeningTujuan = (jenis === 'Transfer / Mutasi') ? String(params.rekeningTujuan || '').trim() : '';

    sheetTransaksi.appendRow([idUnik, tanggal, jenis, kategori, nominal, keterangan, rekeningAsal, rekeningTujuan]);

    if (Array.isArray(params.items) && params.items.length > 0) {
      const sheetDetail = ss.getSheetByName("Detail_Transaksi");
      const detailRows = [];

      params.items.forEach((item, idx) => {
        const idDetail = "DET-" + Date.now() + "-" + (idx + 1);
        const namaBarang = String(item.nama || '').trim();
        const jumlah = Number(item.jumlah) || 1;
        const hargaSatuan = Number(item.hargaSatuan) || 0;
        const diskon = Number(item.diskon) || 0;
        const subtotal = Number(item.subtotal) || ((jumlah * hargaSatuan) - diskon);

        if (namaBarang) {
          detailRows.push([idUnik, idDetail, namaBarang, jumlah, hargaSatuan, diskon, subtotal]);
        }
      });

      if (detailRows.length > 0) {
        sheetDetail.getRange(sheetDetail.getLastRow() + 1, 1, detailRows.length, 7).setValues(detailRows);
      }
    }

    return {
      success: true,
      message: "Transaksi " + jenis + " berhasil disimpan!",
      id: idUnik
    };
  },

  /**
   * Menghapus transaksi dan seluruh detail struk barangnya.
   */
  hapusTransaksi: function(params) {
    const id = typeof params === 'object' ? params.id : params;
    if (!id) throw new Error("ID Transaksi tidak valid.");

    const ss = this.initSheets();
    const sheetTrx = ss.getSheetByName("Transaksi");

    const lastRow = sheetTrx.getLastRow();
    if (lastRow < 2) throw new Error("Data transaksi tidak ditemukan.");

    const data = sheetTrx.getRange(2, 1, lastRow - 1, 1).getValues();
    let rowIndexToDelete = -1;

    for (let i = 0; i < data.length; i++) {
      if (String(data[i][0]).trim() === String(id).trim()) {
        rowIndexToDelete = i + 2;
        break;
      }
    }

    if (rowIndexToDelete === -1) throw new Error("Transaksi tidak ditemukan.");

    sheetTrx.deleteRow(rowIndexToDelete);

    // Hapus juga rincian dari sheet Detail_Transaksi jika ada
    const sheetDetail = ss.getSheetByName("Detail_Transaksi");
    if (sheetDetail && sheetDetail.getLastRow() >= 2) {
      const lastRowDet = sheetDetail.getLastRow();
      const detData = sheetDetail.getRange(2, 1, lastRowDet - 1, 1).getValues();

      for (let j = detData.length - 1; j >= 0; j--) {
        if (String(detData[j][0]).trim() === String(id).trim()) {
          sheetDetail.deleteRow(j + 2);
        }
      }
    }

    return {
      success: true,
      message: "Transaksi berhasil dihapus!"
    };
  },

  tambahKategori: function(params) {
    if (!params || !params.kategori || params.limit === undefined || params.limit === null || params.limit === '') {
      throw new Error("Mohon isi Nama Kategori dan Limit Anggaran.");
    }

    const kategori = String(params.kategori).trim();
    const limit = Number(params.limit) || 0;

    if (!kategori) throw new Error("Nama kategori tidak boleh kosong.");
    if (limit <= 0) throw new Error("Limit anggaran harus lebih besar dari 0.");

    const ss = this.initSheets();
    const sheet = ss.getSheetByName("Anggaran");

    const lastRow = sheet.getLastRow();
    if (lastRow >= 2) {
      const existingCategories = sheet.getRange(2, 1, lastRow - 1, 1).getValues();
      for (let i = 0; i < existingCategories.length; i++) {
        const item = String(existingCategories[i][0] || '').trim();
        if (item.toLowerCase() === kategori.toLowerCase()) {
          throw new Error('Kategori "' + kategori + '" sudah ada di Sheet Anggaran.');
        }
      }
    }

    sheet.appendRow([kategori, limit]);

    return {
      success: true,
      message: 'Kategori "' + kategori + '" berhasil ditambahkan!',
      kategori: kategori,
      limit: limit
    };
  }
};
