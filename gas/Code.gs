/**
 * File Router / Entry Point untuk Google Apps Script Web App
 */

function doGet(e) {
  return HtmlService.createHtmlOutputFromFile('Index')
    .setTitle('Pencatatan Keuangan & Budgeting')
    .setXFrameOptionsMode(HtmlService.XFrameOptionsMode.ALLOWALL)
    .addMetaTag('viewport', 'width=device-width, initial-scale=1.0');
}

/**
 * Jembatan tunggal (Bridge Gateway) untuk mengeksekusi fungsi di LOGIC backend.
 * Frontend memanggil fungsi ini melalui google.script.run
 * 
 * @param {string} funcName - Nama fungsi yang ada di dalam objek LOGIC
 * @param {Array|*} params - Parameter yang akan diteruskan ke fungsi
 * @return {*} Hasil pengembalian dari fungsi LOGIC yang dipanggil
 */
function callServer(funcName, params) {
  try {
    if (!LOGIC || typeof LOGIC[funcName] !== 'function') {
      throw new Error('Fungsi "' + funcName + '" tidak ditemukan pada backend LOGIC.');
    }
    
    var args = Array.isArray(params) ? params : (params !== undefined ? [params] : []);
    return LOGIC[funcName].apply(LOGIC, args);
  } catch (err) {
    Logger.log('Error pada callServer (' + funcName + '): ' + err.toString());
    throw new Error(err.message || err.toString());
  }
}
