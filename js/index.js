var target = null;
var spinner = null;
var loading = false;
app.run(function($rootScope, $templateCache) {
   // Execute every time a state change begins
$rootScope.$on('$stateChangeStart', function (event, toState, toParams, fromState, fromParams) {
// If the state we are headed to has cached template views
if (typeof (toState) !== 'undefined' && typeof (toState.views) !== 'undefined') {
// Loop through each view in the cached state
for (var key in toState.views) {
// Delete templeate from cache
console.log("Delete cached template: " + toState.views[key].templateUrl);
$cacheFactory.get('templates').remove(toState.views[key].templateUrl);
}
}
});
});
function importLibrary() {
    intel.xdk.camera.clearPictures();
    intel.xdk.camera.importPicture();
}

function captureCamera() {
    intel.xdk.camera.clearPictures();
    intel.xdk.camera.takePicture(10 ,true, "jpg");
}

function uploadComplete(result) {
    spinner.stop();
    if (!loading) return;
    loading = false;
    var albumId = store.get('album');
    if(result.success==true) {
        jqalert("a imaxe " + result.localURL + " subiuse correctamente");
        window.history.back();
    }
    else {
        alert("houbo algun erro no proceso " + result.message);
    }
}

function updateUploadProgress(bytesSent,totalBytes) {
    if(totalBytes > 0) currentProgress=(bytesSent/totalBytes)*100;
    $('#progress').val(currentProgress);
}

function addPicture(event){
    if (loading) return;
    loading = false;
    target = document.getElementById('spinner');
    spinner = new Spinner().spin();
    target.appendChild(spinner.el);
    loading = true;
    var name = event.filename;
    var url = intel.xdk.camera.getPictureURL(name);
    var user = store.get('wayhoy_user');
    var pass = store.get('wayhoy_pass');
    var hash = cryptofoo.hash('md5', user + pass );
    var album = store.get('album');
    var uploadUrl = 'https://wayhoy.com/mobile/upload.php?u=' + encodeURIComponent(user) + '&t=' + hash;
    if (album) uploadUrl += '&a=' + album;
    intel.xdk.file.uploadToServer(url, uploadUrl, "", "image/jpeg", "updateUploadProgress");
}

function uploadCancelled() {
    spinner.stop();
    loading = false;
}
$('.ng-scope').on('click', '*', function() {
	console.log('hola');
})
document.addEventListener("intel.xdk.camera.picture.add", addPicture);
document.addEventListener("intel.xdk.file.upload", uploadComplete);
document.addEventListener("intel.xdk.file.upload.cancel", uploadCancelled);