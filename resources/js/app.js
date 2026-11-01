// Portal BEM Terpadu - Script Interaktif Publik

document.addEventListener('DOMContentLoaded', () => {
    // 1. Mobile Menu Toggle
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuOpenIcon = document.getElementById('menu-icon-open');
    const menuCloseIcon = document.getElementById('menu-icon-close');

    if (mobileMenuButton && mobileMenu) {
        mobileMenuButton.addEventListener('click', () => {
            const isExpanded = mobileMenuButton.getAttribute('aria-expanded') === 'true';
            mobileMenuButton.setAttribute('aria-expanded', String(!isExpanded));
            mobileMenu.classList.toggle('hidden');

            if (menuOpenIcon && menuCloseIcon) {
                menuOpenIcon.classList.toggle('hidden');
                menuCloseIcon.classList.toggle('hidden');
            }
        });

        // Close mobile menu on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !mobileMenu.classList.contains('hidden')) {
                mobileMenu.classList.add('hidden');
                mobileMenuButton.setAttribute('aria-expanded', 'false');
                if (menuOpenIcon && menuCloseIcon) {
                    menuOpenIcon.classList.remove('hidden');
                    menuCloseIcon.classList.add('hidden');
                }
                mobileMenuButton.focus();
            }
        });
    }

    // 2. FAQ Accordion Accessible Toggle
    const faqButtons = document.querySelectorAll('[data-faq-button]');
    faqButtons.forEach((btn) => {
        btn.addEventListener('click', () => {
            const contentId = btn.getAttribute('aria-controls');
            const content = document.getElementById(contentId);
            const icon = btn.querySelector('[data-faq-icon]');
            const isExpanded = btn.getAttribute('aria-expanded') === 'true';

            if (content) {
                if (isExpanded) {
                    btn.setAttribute('aria-expanded', 'false');
                    content.classList.add('hidden');
                    if (icon) {
                        icon.classList.remove('rotate-180');
                    }
                } else {
                    btn.setAttribute('aria-expanded', 'true');
                    content.classList.remove('hidden');
                    if (icon) {
                        icon.classList.add('rotate-180');
                    }
                }
            }
        });
    });
});
