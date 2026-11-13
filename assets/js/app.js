/* FindIt! — interaksi ringan tanpa library */
(function () {
    'use strict';

    /* ---------- Menu navigasi mobile ---------- */
    var toggle = document.querySelector('.nav-toggle');
    var menu = document.getElementById('site-menu');

    function closeMenu() {
        if (!toggle || !menu) {
            return;
        }
        toggle.setAttribute('aria-expanded', 'false');
        menu.classList.remove('is-open');
    }

    if (toggle && menu) {
        toggle.addEventListener('click', function () {
            var open = toggle.getAttribute('aria-expanded') === 'true';
            toggle.setAttribute('aria-expanded', open ? 'false' : 'true');
            menu.classList.toggle('is-open', !open);
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && menu.classList.contains('is-open')) {
                closeMenu();
                toggle.focus();
            }
        });

        document.addEventListener('click', function (event) {
            if (!menu.classList.contains('is-open')) {
                return;
            }
            if (!menu.contains(event.target) && !toggle.contains(event.target)) {
                closeMenu();
            }
        });
    }

    /* ---------- Tampilkan field lokasi penyimpanan ---------- */
    var storageWrap = document.querySelector('[data-storage-wrap]');
    var storageInput = storageWrap ? storageWrap.querySelector('input, textarea, select') : null;
    var typeRadios = document.querySelectorAll('input[name="type"]');

    function syncStorageField() {
        if (!storageWrap) {
            return;
        }
        var checked = document.querySelector('input[name="type"]:checked');
        var isFound = checked !== null && checked.value === 'ditemukan';
        storageWrap.hidden = !isFound;
        if (storageInput) {
            if (isFound) {
                storageInput.setAttribute('required', 'required');
            } else {
                storageInput.removeAttribute('required');
            }
        }
    }

    if (typeRadios.length > 0) {
        Array.prototype.forEach.call(typeRadios, function (radio) {
            radio.addEventListener('change', syncStorageField);
        });
        syncStorageField();
    }

    /* ---------- Pratinjau foto & validasi file ---------- */
    var fileInputs = document.querySelectorAll('input[type="file"][data-preview]');

    Array.prototype.forEach.call(fileInputs, function (input) {
        input.addEventListener('change', function () {
            var boxId = input.getAttribute('data-preview');
            var box = boxId ? document.getElementById(boxId) : null;
            var img = box ? box.querySelector('img') : null;
            var errorBox = document.querySelector('[data-file-error]');
            var errorText = document.querySelector('[data-file-error-text]');
            var file = input.files && input.files[0] ? input.files[0] : null;

            if (errorBox) {
                errorBox.hidden = true;
            }
            if (errorText) {
                errorText.textContent = '';
            }

            if (!file) {
                if (box) {
                    box.hidden = true;
                }
                return;
            }

            var allowed = ['image/jpeg', 'image/png', 'image/webp'];
            if (allowed.indexOf(file.type) === -1) {
                if (errorText) {
                    errorText.textContent = 'Format foto harus JPG, PNG, atau WEBP.';
                }
                if (errorBox) {
                    errorBox.hidden = false;
                }
                input.value = '';
                if (box) {
                    box.hidden = true;
                }
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                if (errorText) {
                    errorText.textContent = 'Ukuran foto maksimal 2 MB.';
                }
                if (errorBox) {
                    errorBox.hidden = false;
                }
                input.value = '';
                if (box) {
                    box.hidden = true;
                }
                return;
            }

            if (!img || !box) {
                return;
            }

            var reader = new FileReader();
            reader.onload = function (event) {
                img.src = String(event.target.result);
                box.hidden = false;
            };
            reader.readAsDataURL(file);
        });
    });

    /* ---------- Modal login untuk kontak terkunci ---------- */
    var loginModal = document.getElementById('login-modal');
    var loginGates = document.querySelectorAll('[data-login-gate]');

    if (loginModal && loginGates.length > 0 && typeof loginModal.showModal === 'function') {
        Array.prototype.forEach.call(loginGates, function (gate) {
            gate.addEventListener('click', function (event) {
                event.preventDefault();
                if (!loginModal.open) {
                    loginModal.showModal();
                }
            });
        });

        loginModal.addEventListener('click', function (event) {
            if (event.target === loginModal) {
                loginModal.close();
            }
        });
    }

    /* ---------- Konfirmasi hapus ---------- */
    var confirmForms = document.querySelectorAll('form[data-confirm]');
    Array.prototype.forEach.call(confirmForms, function (form) {
        form.addEventListener('submit', function (event) {
            var message = form.getAttribute('data-confirm') || 'Lanjutkan?';
            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });

    /* ---------- Menu akun (pojok kanan) ---------- */
    var userToggle = document.getElementById('user-menu-toggle');
    var userMenu = document.getElementById('user-menu');

    function closeUserMenu() {
        if (!userToggle || !userMenu) {
            return;
        }
        userToggle.setAttribute('aria-expanded', 'false');
        userMenu.hidden = true;
    }

    if (userToggle && userMenu) {
        userToggle.addEventListener('click', function (event) {
            event.stopPropagation();
            var open = userToggle.getAttribute('aria-expanded') === 'true';
            userToggle.setAttribute('aria-expanded', open ? 'false' : 'true');
            userMenu.hidden = open;
        });

        document.addEventListener('click', function (event) {
            if (userMenu.hidden) {
                return;
            }
            if (!userMenu.contains(event.target) && !userToggle.contains(event.target)) {
                closeUserMenu();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && !userMenu.hidden) {
                closeUserMenu();
                userToggle.focus();
            }
        });
    }
})();
