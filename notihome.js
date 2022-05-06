var toastElements = document.querySelectorAll('.toast')
for (var i = 0; i < toastElements.length; i++) {
    new bootstrap.Toast(toastElements[i]).show();
}