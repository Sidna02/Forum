document.querySelector('.img-btn').addEventListener('click', function()
	{
		document.querySelector('.cont').classList.toggle('s-signup')
	}
);

const hamburger = document.querySelector('.header .nav-bar .nav-list .hamburger');
const mobile_menu = document.querySelector('.header .nav-bar .nav-list ul');
const menu_item = document.querySelectorAll('.header .nav-bar .nav-list ul li a');
const header = document.querySelector('.header.container');

hamburger.addEventListener('click', () => {
	hamburger.classList.toggle('active');
	mobile_menu.classList.toggle('active');
});

document.addEventListener('scroll', () => {
	var scroll_position = window.scrollY;
	if (scroll_position > 250) {
		header.style.backgroundColor = '#29323c';
	} else {
		header.style.backgroundColor = 'transparent';
	}
});

menu_item.forEach((item) => {
	item.addEventListener('click', () => {
		hamburger.classList.toggle('active');
		mobile_menu.classList.toggle('active');
	});
});

const pswrd_1 = document.querySelector("#pswrd_1");
      const pswrd_2 = document.querySelector("#pswrd_2");
      const errorText = document.querySelector(".error-text");
      const showBtn = document.querySelector(".show");
      const btn = document.querySelector("button");
      function active(){
        if(pswrd_1.value.length >= 6){
          btn.removeAttribute("submit", "");
          btn.classList.add("active");
          pswrd_2.removeAttribute("submit", "");
        }else{
          btn.setAttribute("submit", "");
          btn.classList.remove("active");
          pswrd_2.setAttribute("submit", "");
        }
      }
      btn.onclick = function(){
        if(pswrd_1.value != pswrd_2.value){
          errorText.style.display = "block";
          errorText.classList.remove("matched");
          errorText.textContent = "Error! Confirm Password Not Match";
          return false;
        }else{
          errorText.style.display = "block";
          errorText.classList.add("matched");
          errorText.textContent = "Nice! Confirm Password Matched";
          return false;
        }
      }
      function active_2(){
        if(pswrd_2.value != ""){
          showBtn.style.display = "block";
          showBtn.onclick = function(){
            if((pswrd_1.type == "password") && (pswrd_2.type == "password")){
              pswrd_1.type = "text";
              pswrd_2.type = "text";
              this.textContent = "Hide";
              this.classList.add("active");
            }else{
              pswrd_1.type = "password";
              pswrd_2.type = "password";
              this.textContent = "Show";
              this.classList.remove("active");
            }
          }
        }else{
          showBtn.style.display = "none";
        }
      }