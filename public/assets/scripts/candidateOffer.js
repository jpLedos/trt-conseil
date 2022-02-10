const filter = document.querySelector("#actived-select");
const candidates = document.querySelectorAll('.jobOffer')
const toogle = document.querySelectorAll('#toogle');

filter.addEventListener("change",(e) => {
    switch (e.target.value) {
        case 'actived':
            candidates.forEach(canditate => {
                if(canditate.classList.contains('actived')) {
                canditate.style.display='';
                } else {
                    canditate.style.display='none'; 
                }
            })     
            break;

        case 'desactived':
            candidates.forEach(canditate => {
                if(!canditate.classList.contains('actived')) {
                canditate.style.display='';
                } else {
                    canditate.style.display='none'; 
                }
            })     
            break;

        default:
            candidates.forEach(canditate => {
                canditate.style.display='';
                })   
            break;

    }
})
