const comment = document.querySelector('#list-comment');
const commentItems = document.querySelectorAll('#list-comment .hinhanh');
let translateY = 0;
let count = 0;
const next = document.querySelector('.next');
const prev = document.querySelector('.prev');

next.addEventListener('click', function (event) {
  event.preventDefault();
  if (count == commentItems.length - 1) {
    return false;
  }
  translateY += -400;
  comment.style.transform = `translateY(${translateY}px)`;
  count++;
});

prev.addEventListener('click', function (event) {
  event.preventDefault();
  if (count == 0) {
    return false;
  }
  translateY += 400;
  comment.style.transform = `translateY(${translateY}px)`;
  count--;
});

// Slider part
let slider = document.querySelector('.slider .list');
let items = document.querySelectorAll('.slider .list .item');
let nextSlider = document.getElementById('next');
let prevSlider = document.getElementById('prev');
let dots = document.querySelectorAll('.slider .dots li');

let lengthItems = items.length - 1;
let active = 0;
nextSlider.onclick = function () {
  active = active + 1 <= lengthItems ? active + 1 : 0;
  reloadSlider();
}
prevSlider.onclick = function () {
  active = active - 1 >= 0 ? active - 1 : lengthItems;
  reloadSlider();
}
let refreshInterval = setInterval(() => { nextSlider.click() }, 5000);

function reloadSlider() {
  slider.style.left = -items[active].offsetLeft + 'px';
  let lastActiveDot = document.querySelector('.slider .dots li.active');
  lastActiveDot.classList.remove('active');
  dots[active].classList.add('active');
  clearInterval(refreshInterval);
  refreshInterval = setInterval(() => { nextSlider.click() }, 5000);
}


dots.forEach((li, key) => {
    li.addEventListener('click', ()=>{
         active = key;
         reloadSlider();
    })
})
window.onresize = function(event) {
    reloadSlider();
};


let preveiwContainer = document.querySelector('.products-preview');
let previewBox = preveiwContainer.querySelectorAll('.preview');

document.querySelectorAll('.products-container .product').forEach(product =>{
  product.onclick = () =>{
    preveiwContainer.style.display = 'flex';
    let name = product.getAttribute('data-name');
    previewBox.forEach(preview =>{
      let target = preview.getAttribute('data-target');
      if(name == target){
        preview.classList.add('active');
      }
    });
  };
});

previewBox.forEach(close =>{
  close.querySelector('.fa-times').onclick = () =>{
    close.classList.remove('active');
    preveiwContainer.style.display = 'none';
  };
});

