// Animación de tarjetas estilo Pinterest con GSAP
window.addEventListener('DOMContentLoaded', () => {
  if (typeof gsap === 'undefined') return;
  const cards = document.querySelector('.cards');
  if (!cards) return;
  const items = cards.querySelectorAll('a');
  gsap.set(items, {opacity:0, y:40, scale:0.98});
  gsap.to(items, {
    opacity: 1,
    y: 0,
    scale: 1,
    stagger: 0.08,
    duration: 0.7,
    ease: 'power3.out',
    onComplete: () => cards.classList.add('gsap-animated')
  });
  // Hover animación extra
  items.forEach(card => {
    card.addEventListener('mouseenter', () => {
      gsap.to(card, {scale:1.05, boxShadow:'0 15px 35px rgba(30,144,255,0.18)', duration:0.25, overwrite:true});
    });
    card.addEventListener('mouseleave', () => {
      gsap.to(card, {scale:1, boxShadow:'', duration:0.25, overwrite:true});
    });
  });
});