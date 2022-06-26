let url = location.href.split("/"); 
let navLinks = document.getElementsByTagName("nav")[0].getElementsByTagName("a");

let i = 0;
let currentPage = url[url.length - 1];
for (i; i < navLinks.length; i++) {
    var lb = navLinks[i].href.split("/");
    if (lb[lb.length - 1] == currentPage) {
        navLinks[i].className = "current";

    }
}