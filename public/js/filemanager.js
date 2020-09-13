/**
 * Open file manager and return selected files.
 * Promise is never resolved if window is closed.
 *
 * @returns Promise<array> Array of selected files with properties:
 *      icon        string
 *      is_file     bool
 *      is_image    bool
 *      name        string
 *      thumb_url   string|null
 *      time        int
 *      url         string
 */
window.filemanager = function filemanager() {
    var url = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : '/filemanager';
    var target = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'FileManager';
    var features = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 'width=900,height=600';
    return new Promise(function (resolve) {
        window.open(url, target, features);
        window.SetUrl = resolve;
    });
};
