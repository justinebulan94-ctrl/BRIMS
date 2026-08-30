document.addEventListener('DOMContentLoaded', () => {
  const hamburger = document.getElementById('hamburger-btn');
  const navMenu = document.getElementById('nav-menu');

  // Mobile Menu Toggle
  if (hamburger && navMenu) {
    hamburger.addEventListener('click', () => {
      hamburger.classList.toggle('active');
      navMenu.classList.toggle('active');
    });

    document.querySelectorAll('.nav-link, .nav-link1').forEach(link => {
      link.addEventListener('click', () => {
        hamburger.classList.remove('active');
        navMenu.classList.remove('active');
      });
    });
  }
});

// Toggle Inline Table/Card Edit Row
function toggleEdit(id) {
  const formRow = document.getElementById('edit-' + id);
  if (formRow) {
    // Check if form is visible
    const isVisible = (formRow.style.display === 'table-row' || formRow.style.display === 'block');
    
    // Desktop uses table-row, Mobile uses block
    const displayStyle = window.innerWidth <= 768 ? 'block' : 'table-row';
    formRow.style.display = isVisible ? 'none' : displayStyle;
  }
}

// Live Image Preview Handler
function previewImage(event, id) {
  const reader = new FileReader();
  reader.onload = function() {
    const output = document.getElementById('preview-' + id);
    if (output) {
      output.src = reader.result;
    }
  };
  if (event.target.files[0]) {
    reader.readAsDataURL(event.target.files[0]);
  }
}

// Confirmation Dialog before deletion
function confirmDelete(id) {
  if (confirm("Are you sure you want to delete this item?")) {
    window.location.href = 'admin-ucao-2.php?delete=' + id;
  }
}

document.addEventListener('DOMContentLoaded', () => {
  const hamburger = document.getElementById('hamburger-btn');
  const navMenu = document.getElementById('nav-menu');

  // Mobile Menu Toggle
  if (hamburger && navMenu) {
    hamburger.addEventListener('click', () => {
      hamburger.classList.toggle('active');
      navMenu.classList.toggle('active');
    });

    document.querySelectorAll('.nav-link, .nav-link1').forEach(link => {
      link.addEventListener('click', () => {
        hamburger.classList.remove('active');
        navMenu.classList.remove('active');
      });
    });
  }
});

// Toggle Inline Table/Card Edit Row
function toggleEdit(id) {
  const formRow = document.getElementById('edit-' + id);
  if (formRow) {
    const isVisible = (formRow.style.display === 'table-row' || formRow.style.display === 'block');
    const displayStyle = window.innerWidth <= 768 ? 'block' : 'table-row';
    formRow.style.display = isVisible ? 'none' : displayStyle;
  }
}

// Live Image Preview Handler
function previewImage(event, id) {
  const reader = new FileReader();
  reader.onload = function() {
    const output = document.getElementById('preview-' + id);
    if (output) {
      output.src = reader.result;
    }
  };
  if (event.target.files[0]) {
    reader.readAsDataURL(event.target.files[0]);
  }
}

// Confirmation Dialog before deletion
function confirmDelete(id) {
  if (confirm("Are you sure you want to delete this item?")) {
    window.location.href = 'admin-ucao-2.php?delete=' + id;
  }
}

