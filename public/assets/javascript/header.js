const hamburger = document.querySelector('.haydora .nav-bar .nav-list .hamburger');
const mobile_menu = document.querySelector('.haydora .nav-bar .nav-list ul');
const menu_item = document.querySelectorAll('.haydora .nav-bar .nav-list ul li a');
const haydora = document.querySelector('.haydora.header-container');

hamburger.addEventListener('click', () => {
	hamburger.classList.toggle('active');
	mobile_menu.classList.toggle('active');
});

document.addEventListener('scroll', () => {
	var scroll_position = window.scrollY;
	if (scroll_position > 250) {
		haydora.style.backgroundColor = '#29323c';
	} else {
		haydora.style.backgroundColor = 'transparent';
	}
});

menu_item.forEach((item) => {
	item.addEventListener('click', () => {
		hamburger.classList.toggle('active');
		mobile_menu.classList.toggle('active');
	});
});