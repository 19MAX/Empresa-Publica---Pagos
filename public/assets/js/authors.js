/**
 * HOME.JS - Funcionalidades para la página de inicio
 * Gestión de autores, eventos y navegación
 */

class HomeManager {
    constructor() {
        this.currentAuthorId = null;
        this.isAnimating = false;
        this.animationDelay = 100;
        this.scrollThreshold = 300;

        this.initializeElements();
        this.bindEvents();
        this.initializeAnimations();
    }

    /**
     * Inicializar referencias a elementos del DOM
     */
    initializeElements() {
        this.elements = {
            welcomeSection: document.getElementById('welcomeSection'),
            eventsSection: document.getElementById('eventsSection'),
            eventsContainer: document.getElementById('eventsContainer'),
            noEventsMessage: document.getElementById('noEventsMessage'),
            backToTopBtn: document.getElementById('backToTopBtn'),
            filterSection: document.querySelector('.filter-section'),
            authorCards: document.querySelectorAll('.author-card'),
            eventItems: document.querySelectorAll('.event-item')
        };
    }

    /**
     * Vincular eventos del DOM
     */
    bindEvents() {
        // Event listeners para scroll
        window.addEventListener('scroll', this.debounce(() => {
            this.toggleBackToTopButton();
        }, 100));

        window.addEventListener('resize', this.debounce(() => {
            this.toggleBackToTopButton();
        }, 250));

        // Event listener para tecla Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.currentAuthorId) {
                this.showWelcome();
            }
        });
    }

    /**
     * Inicializar animaciones de entrada
     */
    initializeAnimations() {
        this.elements.authorCards.forEach((card, index) => {
            this.animateCardEntrance(card, index);
        });
    }

    /**
     * Animar entrada de tarjetas de autores
     */
    animateCardEntrance(card, index) {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';

        setTimeout(() => {
            card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * this.animationDelay);
    }

    /**
     * Filtrar eventos por autor
     */
    async filterEventsByAuthor(authorId, authorCard, authorName) {
        if (this.isAnimating) return;

        this.isAnimating = true;
        this.currentAuthorId = authorId;

        try {
            // Actualizar estado visual de las cards
            this.updateAuthorCardsState(authorCard);

            // Ocultar bienvenida y mostrar eventos
            await this.transitionToEventsSection(authorName);

            // Filtrar y mostrar eventos
            await this.showAuthorEvents(authorId);

            // Scroll suave a la sección
            this.scrollToEventsSection();

        } catch (error) {
            console.error('Error al filtrar eventos:', error);
        } finally {
            this.isAnimating = false;
        }
    }

    /**
     * Actualizar estado visual de las cards de autor
     */
    updateAuthorCardsState(selectedCard) {
        this.elements.authorCards.forEach(card => {
            card.classList.remove('active');
            card.setAttribute('aria-selected', 'false');
        });

        selectedCard.classList.add('active');
        selectedCard.setAttribute('aria-selected', 'true');
    }

    /**
     * Transición a la sección de eventos
     */
    async transitionToEventsSection(authorName) {
        // Ocultar mensaje de bienvenida
        this.elements.welcomeSection.style.display = 'none';

        // Mostrar sección de eventos
        this.elements.eventsSection.style.display = 'block';

        // Esperar un frame para aplicar animación
        await this.waitForNextFrame();

        this.elements.eventsSection.classList.add('show');
    }

    /**
     * Mostrar eventos del autor seleccionado
     */
    async showAuthorEvents(authorId) {
        // Ocultar todos los eventos inicialmente
        this.hideAllEvents();

        // Encontrar eventos del autor
        const authorEvents = Array.from(this.elements.eventItems)
            .filter(item => item.dataset.authorId == authorId);

        if (authorEvents.length === 0) {
            this.showNoEventsMessage();
            return;
        }

        // Ocultar mensaje de "no eventos"
        this.elements.noEventsMessage.style.display = 'none';

        // Mostrar eventos con animación escalonada
        await this.animateEventsEntrance(authorEvents);
    }

    /**
     * Ocultar todos los eventos
     */
    hideAllEvents() {
        this.elements.eventItems.forEach(item => {
            item.style.display = 'none';
        });
    }

    /**
     * Mostrar mensaje de no eventos
     */
    showNoEventsMessage() {
        this.elements.noEventsMessage.style.display = 'flex';
    }

    /**
     * Animar entrada de eventos
     */
    async animateEventsEntrance(events) {
        await this.delay(300);

        events.forEach((item, index) => {
            setTimeout(() => {
                item.style.display = 'block';
                item.style.opacity = '0';
                item.style.transform = 'translateY(30px)';

                setTimeout(() => {
                    item.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
                    item.style.opacity = '1';
                    item.style.transform = 'translateY(0)';
                }, 50);
            }, index * this.animationDelay);
        });
    }

    /**
     * Scroll suave a la sección de eventos
     */
    scrollToEventsSection() {
        setTimeout(() => {
            this.elements.eventsSection.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }, 500);
    }

    /**
     * Mostrar sección de bienvenida
     */
    async showWelcome() {
        if (this.isAnimating) return;

        this.isAnimating = true;
        this.currentAuthorId = null;

        try {
            // Limpiar estado de las cards
            this.clearAuthorCardsState();

            // Ocultar sección de eventos
            await this.transitionToWelcomeSection();

            // Scroll a la sección de filtros
            this.scrollToFilterSection();

        } catch (error) {
            console.error('Error al mostrar bienvenida:', error);
        } finally {
            this.isAnimating = false;
        }
    }

    /**
     * Limpiar estado de las cards de autor
     */
    clearAuthorCardsState() {
        this.elements.authorCards.forEach(card => {
            card.classList.remove('active');
            card.setAttribute('aria-selected', 'false');
        });
    }

    /**
     * Transición a la sección de bienvenida
     */
    async transitionToWelcomeSection() {
        // Remover animación de eventos
        this.elements.eventsSection.classList.remove('show');

        await this.delay(300);

        // Ocultar sección de eventos
        this.elements.eventsSection.style.display = 'none';

        // Mostrar mensaje de bienvenida
        this.elements.welcomeSection.style.display = 'block';

        // Ocultar todos los eventos
        this.hideAllEvents();
    }

    /**
     * Scroll a la sección de filtros
     */
    scrollToFilterSection() {
        this.elements.filterSection.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });
    }

    /**
     * Scroll suave hacia arriba
     */
    scrollToTop() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    /**
     * Mostrar/ocultar botón flotante según scroll
     */
    toggleBackToTopButton() {
        const shouldShow = this.elements.eventsSection.style.display !== 'none' &&
            window.pageYOffset > this.scrollThreshold;

        this.elements.backToTopBtn.classList.toggle('show', shouldShow);
    }

    /**
     * Utilidades
     */

    // Debounce para optimizar eventos de scroll
    debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Delay promisificado
    delay(ms) {
        return new Promise(resolve => setTimeout(resolve, ms));
    }

    // Esperar al siguiente frame de animación
    waitForNextFrame() {
        return new Promise(resolve => requestAnimationFrame(resolve));
    }

    /**
     * Métodos públicos para acceso global
     */
    static getInstance() {
        if (!window.homeManagerInstance) {
            window.homeManagerInstance = new HomeManager();
        }
        return window.homeManagerInstance;
    }
}

// Funciones globales para mantener compatibilidad con el HTML existente
window.filterEventsByAuthor = function (authorId, authorCard, authorName) {
    const manager = HomeManager.getInstance();
    manager.filterEventsByAuthor(authorId, authorCard, authorName);
};

window.showWelcome = function () {
    const manager = HomeManager.getInstance();
    manager.showWelcome();
};

window.scrollToTop = function () {
    const manager = HomeManager.getInstance();
    manager.scrollToTop();
};

// Inicialización cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function () {
    // Prevenir menú contextual
    document.addEventListener('contextmenu', function (e) {
        e.preventDefault();
    });

    // Inicializar gestor principal
    HomeManager.getInstance();
});

// Exportar para uso en módulos ES6 si es necesario
if (typeof module !== 'undefined' && module.exports) {
    module.exports = HomeManager;
}