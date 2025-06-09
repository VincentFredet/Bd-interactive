const nuage = document.querySelector('.nuage');
const scenes = document.querySelectorAll('.scene');
let previousScene = null;
let lastMouseX = 0;
let lastMouseY = 0;
let currentAudios = [];
let soundEnabled = false;

const soundIcon = document.getElementById('sound-icon');
const toggleSound = () => {
  soundEnabled = !soundEnabled;
  soundIcon.src = soundEnabled ? 'volume.png' : 'mute.png';

};

document.getElementById('sound-toggle').addEventListener('click', toggleSound);
const sceneSounds = {
    2: 'son1.mp3',
    4: 'son2.mp3',
    5: 'montee.mp3',
    6: 'montee.mp3',
    7: 'montee.mp3',
    8: 'sons3.mp3',
    9: ['sons4.mp3','sons5.mp3'],
  };
const sceneLocks = {
    2: false,
    3: false,
    4: false
  };
window.addEventListener('load', () => {
    // On autorise temporairement le scroll
    document.body.style.overflowX = 'auto';
    setTimeout(() => {
        window.scrollTo({ left: 0, top: 0, behavior: 'instant' });
      }, 200); // petit délai pour que le scroll ait eu lieu
    // On force le scroll tout à gauche

  
    // On rebloque juste après
    setTimeout(() => {
      document.body.style.overflowX = 'hidden';
    }, 500); // petit délai pour que le scroll ait eu lieu
  });

  const lightImg = document.querySelector('.scene[data-scene="1"] .light');
  const unlockZone = document.getElementById('unlock-zone');
  const offsetLeftrecup = lightImg.getBoundingClientRect().left;

  // On attend que l’image soit bien chargée pour récupérer son offset
  if (lightImg.complete) {
    positionZone();
  } else {
    lightImg.addEventListener('load', positionZone);
  }

  function positionZone() {
    const offsetLeft = lightImg.getBoundingClientRect().left + window.scrollX;
    unlockZone.style.left = `${offsetLeft + 240}px`;
  }

  // Bloque le scroll au début
  document.body.style.overflowX = 'hidden';

  // Quand on survole la zone → débloque le scroll et masque #intro
  unlockZone.addEventListener('mouseenter', () => {
    document.body.style.overflowX = 'auto';
});
function updateNuage(x, y) {
    const globalX = x + window.scrollX;
    const globalY = y;

    let currentScene = null;
    let sceneOffsetLeft = 0;

    scenes.forEach(scene => {
        const rect = scene.getBoundingClientRect();
        const absLeft = rect.left + window.scrollX;
        const absRight = absLeft + rect.width;

        if (globalX >= absLeft && globalX <= absRight) {
            currentScene = scene.dataset.scene;
            sceneOffsetLeft = absLeft;
        }
    });
let bonusleft = 280;
let bonustop = 370
if (currentScene !== previousScene) {
    // On a changé de scène ! 🔁
  
    // Réinitialise le verrou de la scène précédente
    if (previousScene && sceneLocks.hasOwnProperty(previousScene)) {
      sceneLocks[previousScene] = false;
    }
    // Si la nouvelle scène a un son à jouer et qu'on ne l'a pas encore joué
    if (soundEnabled && sceneSounds[currentScene] && !sceneLocks[currentScene]) {
        currentAudios.forEach(audio => {
            audio.pause();
            audio.currentTime = 0;
          });
          currentAudios = []; // on vide le tableau
        sceneLocks[currentScene] = true;
        setTimeout(() => {
            if (typeof sceneSounds[currentScene] === 'string') {
            const repetitions = currentScene == 5 ? 1 : currentScene == 6 ? 2 : currentScene == 7 ? 3 : 1;
            const speed = repetitions; // 1x, 2x, 3x
            
            for (let i = 0; i < repetitions; i++) {
              const audio = new Audio(sceneSounds[currentScene]);
              audio.playbackRate = speed;
        
              // Délai entre chaque son accéléré pour qu’ils se suivent bien
              audio.play().then(() => {
              });
              currentAudios.push(audio);
            }
        }
            else if (Array.isArray(sceneSounds[currentScene])) {
                sceneSounds[currentScene].forEach(path => {
                  const audio = new Audio(path);
                  audio.play();
                  currentAudios.push(audio);
                });
              }
          }, 500);
        }
        
    
    // Met à jour la scène actuelle
    previousScene = currentScene;
  }
  
    if (currentScene) {
        let active = document.getElementById('nuage1');
        let inactive = document.getElementById('nuage2');
        let inactive2 = document.getElementById('nuage3');
        if (currentScene == 2) {
            inactive = document.getElementById('nuage1');
            inactive2 = document.getElementById('nuage2');
        }
        if (currentScene == 3) {
            active = document.getElementById('nuage2');
            inactive = document.getElementById('nuage1');
            inactive2 = document.getElementById('nuage3');
            bonusleft = 230;
            bonustop = 350;
        }
        if (currentScene == 4) {
            active = document.getElementById('nuage3');
            inactive = document.getElementById('nuage2');
            inactive2 = document.getElementById('nuage4');
            bonusleft = 180;
            bonustop = 330;
        }
        if (currentScene > 4) {
            active = document.getElementById('nuage4');
            inactive = document.getElementById('nuage3');
            inactive2 = document.getElementById('nuage5');
            bonusleft = 100;
            bonustop = 290;
        }
        if (currentScene != 2) {
            active.classList.add('active');
            document.getElementById(`${active.id}-contour`).classList.add('active');
        }
        
        // Retire la classe active des autres
        inactive.classList.remove('active');
        inactive2.classList.remove('active');
        document.getElementById(`${inactive.id}-contour`).classList.remove('active');
        document.getElementById(`${inactive2.id}-contour`).classList.remove('active');
        if (currentScene == 9 ||currentScene == 10 || currentScene == 'final') {
            document.querySelectorAll('.nuage').forEach(n => n.classList.remove('active'));
            document.querySelectorAll('.nuage.contour').forEach(n => n.classList.remove('active'));
        }
        if (currentScene === 'final') {
            const video = document.getElementById('video-final');
            if (!video.dataset.played) {
              video.src = 'salle_attente.mp4'; // ton fichier vidéo
              video.play();
              video.dataset.played = 'true'; // empêche la relance
            }
          }
          if (currentScene == 8) {
            active = document.getElementById('nuage5');
            inactive = document.getElementById('nuage4');
            inactive2 = document.getElementById('nuage6');
            active.classList.add('active');
            document.getElementById(`${active.id}-contour`).classList.add('active');
            bonusleft = 0;
            bonustop = 280;
            inactive.classList.remove('active');
        inactive2.classList.remove('active')
        document.getElementById(`${inactive.id}-contour`).classList.remove('active');
        document.getElementById(`${inactive2.id}-contour`).classList.remove('active');
        }
          if (currentScene == 11) {
            active = document.getElementById('nuage6');
            inactive = document.getElementById('nuage5');
            active.classList.add('active');
            document.getElementById(`${active.id}-contour`).classList.add('active');
            bonusleft = 0;
            bonustop = 280;
            inactive.classList.remove('active');
            document.getElementById(`${inactive.id}-contour`).classList.remove('active');
        }

const left = globalX - active.offsetWidth / 2+bonusleft;
const top = globalY - active.offsetHeight / 2+bonustop;
active.style.left = `${left}px`;
active.style.top = `${top}px`;
const contour = document.getElementById(`${active.id}-contour`);
contour.style.left = `${left}px`;
contour.style.top = `${top}px`;

// Mettre à jour le fond associé à la scène
active.style.backgroundImage = `url('hover${currentScene}.png')`;
// Centrer aussi le background par rapport au curseur
let bgX = -(globalX - sceneOffsetLeft - active.offsetWidth / 2)-bonusleft;
if (currentScene == 1) {
    console.log(offsetLeftrecup)
bgX += offsetLeftrecup;
  }
  const vhToPx = window.innerHeight * 0.05;
let bgY = -(globalY - active.offsetHeight / 2)-bonustop+19 + vhToPx;
active.style.backgroundPosition = `${bgX}px ${bgY}px`;

    } else {
        document.querySelectorAll('.nuage').forEach(n => n.classList.remove('active'));
        document.querySelectorAll('.nuage.contour').forEach(n => n.classList.remove('active'));
    }
}

// 🎯 Quand la souris bouge → on met à jour + on enregistre sa position
document.addEventListener('mousemove', (e) => {
    lastMouseX = e.clientX;
    lastMouseY = e.clientY;
    updateNuage(lastMouseX, lastMouseY);
});

// 🌀 Quand on scroll → on remet à jour avec les dernières coordonnées souris
document.addEventListener('scroll', () => {
    updateNuage(lastMouseX, lastMouseY);
}, { passive: true });
