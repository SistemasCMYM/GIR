import './bootstrap';

import swal from 'sweetalert2'; 
window.Swal = swal;

const MOBILE_BREAKPOINT = 1040;

const DashboardApp = (() => {
    let body;
    let toggleButtons = [];
    let overlay;
    let sidebarMenu;
    let navMenuEl;
    let desktopState = 'default';

    const isMobileView = () => window.innerWidth < MOBILE_BREAKPOINT;

    const cacheDom = () => {
        body = document.body || null;
        if (!body) {
            return;
        }

        toggleButtons = Array.from(document.querySelectorAll('.button-toggle-menu'));
        overlay = document.querySelector('[data-sidebar-dismiss]');
        sidebarMenu = document.querySelector('.app-sidebar-menu');
        navMenuEl = document.querySelector('#sidebar-menu') || document.querySelector('#side-menu');
        desktopState = body.getAttribute('data-leftbar-size') || 'default';
    };

    const applySidebarState = (state, preserveDesktop = false) => {
        if (!body) {
            return;
        }

        const mobileView = isMobileView();

        if (!preserveDesktop && state !== 'hidden') {
            desktopState = state;
        }

        body.setAttribute('data-sidebar', state);
        body.setAttribute('data-leftbar-size', state);

        if (state === 'hidden' || !mobileView) {
            body.classList.remove('sidebar-enable');
        } else if (mobileView && state !== 'hidden') {
            body.classList.add('sidebar-enable');
        }

        if (overlay) {
            const showOverlay = mobileView && state !== 'hidden';
            overlay.classList.toggle('is-visible', showOverlay);
        }
    };

    const closeOtherCollapses = (current) => {
        if (typeof bootstrap === 'undefined' || !navMenuEl) {
            return;
        }

        const opened = navMenuEl.querySelectorAll('.collapse.show');
        opened.forEach((collapseEl) => {
            if (collapseEl === current || collapseEl.contains(current)) {
                return;
            }
            const instance = bootstrap.Collapse.getInstance(collapseEl);
            if (instance) {
                instance.hide();
            }
        });
    };

    const bindNavMenu = () => {
        if (!navMenuEl) {
            return;
        }

        if (typeof bootstrap !== 'undefined') {
            navMenuEl.addEventListener('show.bs.collapse', (event) => {
                closeOtherCollapses(event.target);
            });
        }

        const currentUrl = window.location.href.split(/[?#]/)[0];
        navMenuEl.querySelectorAll('a').forEach((link) => {
            if (link.href === currentUrl) {
                link.classList.add('active');

                let ancestor = link.parentElement;
                while (ancestor && ancestor !== navMenuEl) {
                    if (ancestor.classList.contains('collapse') && typeof bootstrap !== 'undefined') {
                        const collapseInstance = bootstrap.Collapse.getOrCreateInstance(ancestor, { toggle: false });
                        collapseInstance.show();
                    }
                    if (ancestor.tagName === 'LI') {
                        ancestor.classList.add('menuitem-active');
                    }
                    ancestor = ancestor.parentElement;
                }
            }

            link.addEventListener('click', () => {
                if (isMobileView()) {
                    applySidebarState('hidden', true);
                }
            });
        });
    };

    const initMenu = () => {
        if (!body) {
            return;
        }

        applySidebarState(desktopState);
        if (isMobileView()) {
            applySidebarState('hidden', true);
        }

        const handleResize = () => {
            if (isMobileView()) {
                applySidebarState('hidden', true);
            } else {
                applySidebarState(desktopState || 'default');
            }
        };

        window.addEventListener('resize', handleResize);

        toggleButtons.forEach((btn) => {
            btn.addEventListener('click', (event) => {
                event.preventDefault();
                if (isMobileView() || btn.classList.contains('button-menu-mobile')) {
                    const isHidden = body.getAttribute('data-sidebar') === 'hidden';
                    applySidebarState(isHidden ? 'default' : 'hidden', true);
                } else {
                    const current = body.getAttribute('data-leftbar-size') || 'default';
                    const next = current === 'condensed' ? 'default' : 'condensed';
                    applySidebarState(next);
                }
            });
        });

        if (overlay) {
            overlay.addEventListener('click', () => applySidebarState('hidden', true));
        }

        if (sidebarMenu) {
            sidebarMenu.addEventListener('mouseleave', () => {
                if (!isMobileView() && body.getAttribute('data-leftbar-size') === 'condensed') {
                    body.setAttribute('data-sidebar', 'condensed');
                }
            });
        }

        document.addEventListener('keyup', (event) => {
            if (event.key === 'Escape' && isMobileView()) {
                applySidebarState('hidden', true);
            }
        });

        bindNavMenu();
    };

    const initComponents = () => {
        if (typeof window !== 'undefined') {
            if (window.Waves && typeof window.Waves.init === 'function') {
                window.Waves.init();
            }
            if (window.feather && typeof window.feather.replace === 'function') {
                window.feather.replace();
            }
        }

        if (typeof bootstrap !== 'undefined') {
            document.querySelectorAll('[data-bs-toggle="popover"]').forEach((el) => new bootstrap.Popover(el));
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach((el) => new bootstrap.Tooltip(el));
            document.querySelectorAll('.toast').forEach((el) => new bootstrap.Toast(el));
        }

        const toastPlacement = document.getElementById('toastPlacement');
        const selectToastPlacement = document.getElementById('selectToastPlacement');
        if (toastPlacement && selectToastPlacement) {
            if (!toastPlacement.dataset.originalClass) {
                toastPlacement.dataset.originalClass = toastPlacement.className;
            }
            selectToastPlacement.addEventListener('change', (event) => {
                toastPlacement.className = `${toastPlacement.dataset.originalClass} ${event.target.value}`.trim();
            });
        }

        const liveAlertPlaceholder = document.getElementById('liveAlertPlaceholder');
        const liveAlertBtn = document.getElementById('liveAlertBtn');
        if (liveAlertBtn && liveAlertPlaceholder) {
            liveAlertBtn.addEventListener('click', () => {
                const wrapper = document.createElement('div');
                wrapper.innerHTML = `
          <div class="alert alert-primary alert-dismissible" role="alert">
            Nice, you triggered this alert message!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>`;
                liveAlertPlaceholder.append(wrapper);
            });
        }
    };

    const initControls = () => {
        const fullscreenToggles = document.querySelectorAll('[data-toggle="fullscreen"]');

        const exitHandler = () => {
            if (
                !document.fullscreenElement &&
                !document.mozFullScreenElement &&
                !document.webkitFullscreenElement &&
                !document.msFullscreenElement
            ) {
                body.classList.remove('fullscreen-enable');
            }
        };

        const requestFullScreen = () => {
            const docEl = document.documentElement;
            if (docEl.requestFullscreen) {
                docEl.requestFullscreen();
            } else if (docEl.mozRequestFullScreen) {
                docEl.mozRequestFullScreen();
            } else if (docEl.webkitRequestFullscreen) {
                docEl.webkitRequestFullscreen();
            } else if (docEl.msRequestFullscreen) {
                docEl.msRequestFullscreen();
            }
        };

        const cancelFullScreen = () => {
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.mozCancelFullScreen) {
                document.mozCancelFullScreen();
            } else if (document.webkitCancelFullScreen) {
                document.webkitCancelFullScreen();
            } else if (document.msExitFullscreen) {
                document.msExitFullscreen();
            }
        };

        fullscreenToggles.forEach((toggle) => {
            toggle.addEventListener('click', (event) => {
                event.preventDefault();
                body.classList.toggle('fullscreen-enable');
                if (
                    document.fullscreenElement ||
                    document.mozFullScreenElement ||
                    document.webkitFullscreenElement ||
                    document.msFullscreenElement
                ) {
                    cancelFullScreen();
                } else {
                    requestFullScreen();
                }
            });
        });

        ['fullscreenchange', 'webkitfullscreenchange', 'mozfullscreenchange', 'MSFullscreenChange'].forEach((eventName) => {
            document.addEventListener(eventName, exitHandler);
        });
    };

    const init = () => {
        cacheDom();
        if (!body) {
            return;
        }

        initComponents();
        initMenu();
        initControls();
    };

    return { init };
})();

document.addEventListener('DOMContentLoaded', () => {
    DashboardApp.init();
});
