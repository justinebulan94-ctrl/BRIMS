const uploadBox = document.getElementById("uploadBox");
const itemPicture = document.getElementById("itemPicture");
const imagePreview = document.getElementById("imagePreview");
const previewImage = document.getElementById("previewImage");
const removeImage = document.getElementById("removeImage");
const assetForm = document.getElementById("assetForm");
const submitBtn = document.getElementById("submitBtn");

// Open file selector
uploadBox.addEventListener("click", () => {
    itemPicture.click();
});

// Display selected image
itemPicture.addEventListener("change", function(){
    const file = this.files[0];
    if(!file) return;

    if(!file.type.startsWith("image/")){
        alert("Please select a JPG or PNG image.");
        this.value = "";
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e){
        previewImage.src = e.target.result;
        imagePreview.classList.add("show");
    };
    reader.readAsDataURL(file);
});

// Remove image
removeImage.addEventListener("click", function(e){
    e.stopPropagation(); // Prevents triggering uploadBox click event
    itemPicture.value = "";
    previewImage.src = "";
    imagePreview.classList.remove("show");
});

// Drag and drop
uploadBox.addEventListener("dragover", function(e){
    e.preventDefault();
    uploadBox.classList.add("dragging");
});

uploadBox.addEventListener("dragleave", function(){
    uploadBox.classList.remove("dragging");
});

uploadBox.addEventListener("drop", function(e){
    e.preventDefault();
    uploadBox.classList.remove("dragging");

    const file = e.dataTransfer.files[0];
    if(!file) return;

    if(!file.type.startsWith("image/")){
        alert("Please upload an image.");
        return;
    }

    itemPicture.files = e.dataTransfer.files;

    const reader = new FileReader();
    reader.onload = function(event){
        previewImage.src = event.target.result;
        imagePreview.classList.add("show");
    };
    reader.readAsDataURL(file);
});

// REMOVED e.preventDefault() so the PHP form can process correctly
assetForm.addEventListener("submit", function(){
    submitBtn.innerHTML = `
        <i class="fa-solid fa-spinner fa-spin"></i>
        ADDING ITEM...
    `;
});

// Records button
document.getElementById("recordsBtn").addEventListener("click", function(){
    window.location.href = "records.html";
});

// Add item button
document.getElementById("addItemBtn").addEventListener("click", function(){
    window.scrollTo({
        top: 0,
        behavior: "smooth"
    });
});
