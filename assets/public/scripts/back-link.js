const backLink = document.getElementById("js-back-link")
if (backLink) {
    backLink.addEventListener("click", () => {
        history.back();
    });
}