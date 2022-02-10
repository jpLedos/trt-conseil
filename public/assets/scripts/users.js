const filter = document.querySelector("#category-select");
const categories = document.querySelectorAll('.category')

filter.addEventListener("change",(e) => {
    
    categories.forEach(category => {
        if(!category.classList.contains(e.target.value) && e.target.value!= "all") {
            category.style.display='none';
        } else {
            category.style.display='';
        }
    })
})
