const imgDiv = document.querySelector('.profile-pic-div');
const img = document.querySelector('#photo');
const file = document.querySelector('#file');
const uploadBtn = document.querySelector('#uploadBtn');

//สมมติว่ามีรูปจากฐานข้อมูล
let backendPhotoProfile = ['read_profile_pic.php']
//โชว์รูปเมื่อมีรูปจากฐานข้อมูล
if (backendPhotoProfile.length == 1){
    backendPhotoProfile.forEach((picpro) =>{
        img.setAttribute("src",picpro)
    })
}

imgDiv.addEventListener('mouseenter',function()
{
    uploadBtn.style.display = 'block';
});

imgDiv.addEventListener('mouseleave',function()
{
    uploadBtn.style.display = 'none';
});

file.addEventListener('change',function(){
    const choosedFile = this.files[0];

    //เก็บไฟล์ภาพจาก choosedFile
    if(choosedFile){
        const reader = new FileReader();
        reader.addEventListener('load',function(){
            img.setAttribute('src',reader.result);
        });
        reader.readAsDataURL(choosedFile);
    }
});