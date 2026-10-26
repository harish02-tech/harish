/**
 * LearnSphere – Interactive Client-Side Logic & Form Validation
 */

document.addEventListener('DOMContentLoaded', () => {
  // 1. Mobile Navigation Toggle
  const mobileToggle = document.getElementById('mobileNavToggle');
  const mainNav = document.getElementById('mainNav');

  if (mobileToggle && mainNav) {
    mobileToggle.addEventListener('click', () => {
      mainNav.classList.toggle('active');
      const icon = mobileToggle.querySelector('i');
      if (icon) {
        if (mainNav.classList.contains('active')) {
          icon.classList.remove('fa-bars');
          icon.classList.add('fa-times');
        } else {
          icon.classList.remove('fa-times');
          icon.classList.add('fa-bars');
        }
      }
    });

    // Close mobile nav when clicking outside
    document.addEventListener('click', (e) => {
      if (!mainNav.contains(e.target) && !mobileToggle.contains(e.target) && mainNav.classList.contains('active')) {
        mainNav.classList.remove('active');
        const icon = mobileToggle.querySelector('i');
        if (icon) {
          icon.classList.remove('fa-times');
          icon.classList.add('fa-bars');
        }
      }
    });
  }

  // 2. Interactive Study Hours Range Slider (register.php)
  const studyHoursRange = document.getElementById('study_hours');
  const studyHoursDisplay = document.getElementById('studyHoursVal');

  if (studyHoursRange && studyHoursDisplay) {
    studyHoursRange.addEventListener('input', (e) => {
      studyHoursDisplay.textContent = `${e.target.value} hrs / week`;
    });
  }

  // 3. Interactive Theme Color Picker Preview (register.php)
  const themeColorInput = document.getElementById('theme_color');
  const colorPreviewBadge = document.getElementById('colorPreviewBadge');

  if (themeColorInput && colorPreviewBadge) {
    themeColorInput.addEventListener('input', (e) => {
      colorPreviewBadge.style.backgroundColor = e.target.value;
      colorPreviewBadge.textContent = e.target.value.toUpperCase();
    });
  }

  // 4. Client-side Form Validation for Student Registration
  const registrationForm = document.getElementById('enrollmentForm');
  if (registrationForm) {
    registrationForm.addEventListener('submit', (e) => {
      let isValid = true;
      let firstErrorField = null;

      // Validate First & Last Name
      const firstName = document.getElementById('first_name');
      const lastName = document.getElementById('last_name');
      if (firstName && firstName.value.trim().length < 2) {
        showFieldError(firstName, 'First name must be at least 2 characters.');
        isValid = false;
        firstErrorField = firstErrorField || firstName;
      } else if (firstName) {
        clearFieldError(firstName);
      }

      if (lastName && lastName.value.trim().length < 2) {
        showFieldError(lastName, 'Last name must be at least 2 characters.');
        isValid = false;
        firstErrorField = firstErrorField || lastName;
      } else if (lastName) {
        clearFieldError(lastName);
      }

      // Validate Email
      const email = document.getElementById('email');
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (email && (!email.value.trim() || !emailRegex.test(email.value.trim()))) {
        showFieldError(email, 'Please enter a valid academic or personal email address.');
        isValid = false;
        firstErrorField = firstErrorField || email;
      } else if (email) {
        clearFieldError(email);
      }

      // Validate Phone
      const phone = document.getElementById('phone');
      if (phone && phone.value.trim().length < 7) {
        showFieldError(phone, 'Please enter a valid telephone number.');
        isValid = false;
        firstErrorField = firstErrorField || phone;
      } else if (phone) {
        clearFieldError(phone);
      }

      // Validate Date of Birth (must be at least 14 years old)
      const dob = document.getElementById('dob');
      if (dob && dob.value) {
        const birthDate = new Date(dob.value);
        const today = new Date();
        const age = today.getFullYear() - birthDate.getFullYear();
        if (birthDate > today || age < 14) {
          showFieldError(dob, 'Student must be at least 14 years of age.');
          isValid = false;
          firstErrorField = firstErrorField || dob;
        } else {
          clearFieldError(dob);
        }
      }

      // Validate Course Selection
      const courseSelect = document.getElementById('course_id');
      if (courseSelect && !courseSelect.value) {
        showFieldError(courseSelect, 'Please select an academic course to enroll.');
        isValid = false;
        firstErrorField = firstErrorField || courseSelect;
      } else if (courseSelect) {
        clearFieldError(courseSelect);
      }

      // Validate Terms Checkbox
      const termsCheckbox = document.getElementById('terms_agree');
      if (termsCheckbox && !termsCheckbox.checked) {
        showFieldError(termsCheckbox, 'You must review and accept the academic integrity policy.');
        isValid = false;
        firstErrorField = firstErrorField || termsCheckbox;
      } else if (termsCheckbox) {
        clearFieldError(termsCheckbox);
      }

      if (!isValid) {
        e.preventDefault();
        if (firstErrorField) {
          firstErrorField.focus();
          firstErrorField.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
      }
    });
  }

  // Helper validation functions
  function showFieldError(field, message) {
    field.classList.add('is-invalid');
    let errorElem = field.parentElement.querySelector('.field-error-msg');
    if (!errorElem) {
      errorElem = document.createElement('small');
      errorElem.className = 'field-error-msg';
      errorElem.style.color = 'var(--danger-color)';
      errorElem.style.marginTop = '4px';
      errorElem.style.fontWeight = '500';
      field.parentElement.appendChild(errorElem);
    }
    errorElem.textContent = message;
  }

  function clearFieldError(field) {
    field.classList.remove('is-invalid');
    const errorElem = field.parentElement.querySelector('.field-error-msg');
    if (errorElem) {
      errorElem.remove();
    }
  }

  // 5. Header scroll elevation effect
  const header = document.querySelector('.site-header');
  if (header) {
    window.addEventListener('scroll', () => {
      if (window.scrollY > 20) {
        header.style.boxShadow = '0 4px 20px rgba(11, 25, 44, 0.12)';
      } else {
        header.style.boxShadow = 'var(--shadow-sm)';
      }
    });
  }
});
