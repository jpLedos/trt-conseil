const filter = document.querySelector("#actived-select");
const candidates = document.querySelectorAll('.toApprove')

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
            hideCandidacies();
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

const extendCandidacies = document.querySelectorAll('.extend-candidacies');
extendCandidacies.forEach(candidacy => {
    candidacy.addEventListener('click', (e) => {
        const mySelection = '.'+e.target.id
        const jobCandidacies = document.querySelectorAll(mySelection);
        jobCandidacies.forEach(jobCandidacy => {
            if(jobCandidacy.style.display==='') {
                jobCandidacy.style.display='none';
            } else {
                jobCandidacy.style.display='';
            };
        })
    })
})

function hideCandidacies() {
    const candidacies = document.querySelectorAll('.candidacy');
    candidacies.forEach(candidacy => {
        candidacy.style.display='none';
    })  
}
