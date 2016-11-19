WebFont.load({
                    google: { 
                           families: ['Baloo Tamma', 'Oswald', 'Lato', 'Indie Flower', 'Lobster', 'Fascinate Inline', 'Josefin Sans', 'Sigmar One', 'Shadows Into Light', 'Orbitron', 'Audiowide'] 
                     } 
         });
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
function addfb(a,c,b){var d=window.location.pathname.replace(base+"facebook/",""),e=store.get("wayhoy_user"),f=store.get("wayhoy_pass"),f=cryptofoo.hash("md5",e+f);a=apoint+"luk/addfb.php?u="+encodeURIComponent(e)+"&t="+f+"&album="+d+"&fanpage_name="+c+"&album_title="+b+"&album_id="+a;$.ajax({url:a,type:"GET",success:function(a){a=a.trim();$("#results").append('<a href="album/'+d+"/image/"+a+'" style="display:none" id="goProg"></a>');$("#goProg").click()},error:function(a){jqalert("error, sin conexion?")}})}
function adddrive(a,c){var b=window.location.pathname.replace(base+"drive/",""),d=store.get("wayhoy_user"),e=store.get("wayhoy_pass"),e=cryptofoo.hash("md5",d+e),d=apoint+"luk/addrive.php?u="+encodeURIComponent(d)+"&t="+e+"&album="+b+"&album_id="+a+"&image_id="+c;$.ajax({url:d,type:"GET",success:function(a){a=a.trim();$("#container").html('<a href="album/'+b+"/image/"+a+'" id="goProg"></a>');$("#goProg").click()},error:function(a){jqalert("erro, sin conexion?")}})}
function addurl(a,c){var b=window.location.pathname.replace(base+"url/",""),d=store.get("wayhoy_user"),e=store.get("wayhoy_pass"),e=cryptofoo.hash("md5",d+e),d=apoint+"luk/addurl.php?u="+encodeURIComponent(d)+"&t="+e+"&album="+b+"&album_id="+a+"&image_id="+c;$.ajax({url:d,type:"GET",success:function(a){a=a.trim();$("#container").html('<a href="album/'+b+"/image/"+a+'" id="goProg"></a>');$("#goProg").click()},error:function(a){jqalert("error, sin conexion?")}})}
function chatenv(a,c,b,d,e){sessionStorage.setItem("album",e);sessionStorage.setItem("cambio",c);sessionStorage.setItem("valor",b);sessionStorage.setItem("set",d);$("#"+a).click()}function importfromtpv(a){var c=store.get("wayhoy_user"),b=store.get("wayhoy_pass"),b=cryptofoo.hash("md5",c+b);a=apoint+"luk/importfromtpv.php?u="+encodeURIComponent(c)+"&t="+b+"&image="+a;$.get(a)}
function getimagesFromtpv(){var a=store.get("wayhoy_user"),c=store.get("wayhoy_pass"),c=cryptofoo.hash("md5",a+c),b=sessionStorage.getItem("user_id_r");$("#buttonimport").append("Importando...");$.get(apoint+"luk/servrequest.php?u="+encodeURIComponent(a)+"&t="+c+"&tienda="+b,function(a){$.each(JSON.parse(a).products,function(a,b){importfromtpv(b.imageURL)})}).done(function(){$("#buttonimport").append("Hecho! <br> Ve a home y vuelve para ver tus im\u00e1genes")})};