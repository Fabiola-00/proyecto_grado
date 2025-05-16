document.addEventListener("DOMContentLoaded", function () {
  // Elementos del DOM
  const toggleBtn = document.querySelector(".toggle-btn");
  const sidebar = document.querySelector(".sidebar");
  const mobileMenuBtn = document.querySelector(".mobile-menu-btn");
  const menuLinks = document.querySelectorAll(".sidebar-menu a");
  const viewContainer = document.getElementById("view-container");
  const viewTitle = document.getElementById("view-title");
  const modalContainer = document.getElementById("modal-container");

  // Estado de la aplicación
  const appState = {
    currentView: "dash_inicio",
    loadedViews: {},
  };

  // Toggle sidebar
  toggleBtn.addEventListener("click", function () {
    sidebar.classList.toggle("collapsed");
    const icon = this.querySelector("i");
    icon.classList.toggle("fa-chevron-left");
    icon.classList.toggle("fa-chevron-right");
  });

  // Mobile menu toggle
  mobileMenuBtn.addEventListener("click", function () {
    sidebar.classList.toggle("show");
  });

  // Cargar vista inicial
  loadView("dash_inicio");

  // Manejar clic en los enlaces del menú
  menuLinks.forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault();
      const viewName = this.getAttribute("data-view");
      if (viewName !== appState.currentView) {
        setActiveLink(this);
        loadView(viewName);
      }

      if (window.innerWidth < 992) {
        sidebar.classList.remove("show");
      }
    });
  });

  // Función para establecer el enlace activo
  function setActiveLink(activeLink) {
    menuLinks.forEach((link) => link.classList.remove("active"));
    activeLink.classList.add("active");
  }

  // Función para cargar una vista
  async function loadView(viewName) {
    try {
      appState.currentView = viewName;
      viewTitle.textContent = document.querySelector(
        `.sidebar-menu a[data-view="${viewName}"] span`
      ).textContent;

      // Mostrar carga
      viewContainer.innerHTML =
        '<div class="loading-spinner"><i class="fas fa-spinner fa-spin"></i></div>';
      viewContainer.setAttribute("data-view", viewName);

      // Cargar la vista
      let viewHTML;
      if (appState.loadedViews[viewName]) {
        viewHTML = appState.loadedViews[viewName];
      } else {
        const response = await fetch(`views/${viewName}.html`);
        if (!response.ok) throw new Error("Vista no encontrada");
        viewHTML = await response.text();
        appState.loadedViews[viewName] = viewHTML;
      }

      viewContainer.innerHTML = viewHTML;
      viewContainer.classList.add("active");

      // Ejecutar scripts de la vista
      executeViewScripts(viewContainer, viewName);
    } catch (error) {
      console.error("Error cargando la vista:", error);
      viewContainer.innerHTML = `
        <div class="alert alert-danger">
          Error al cargar la vista: ${error.message}
        </div>
      `;
    }
  }

  // Ejecutar scripts incrustados en la vista
  function executeViewScripts(container, viewName) {
    const scripts = container.querySelectorAll("script");
    scripts.forEach((script) => {
      // Crear un nuevo script
      const newScript = document.createElement("script");

      // Copiar atributos del script original
      if (script.src) {
        newScript.src = script.src;
      } else {
        newScript.textContent = script.textContent;
      }

      // Ejecutar el script
      document.body.appendChild(newScript);
      document.body.removeChild(newScript);
    });

    // Ejecutar la inicialización de la vista si existe
    if (window.initDashboard) {
      window.initDashboard();
    }
  }

  // Manejar redimensionamiento
  function handleResponsive() {
    if (window.innerWidth < 992) {
      mobileMenuBtn.style.display = "block";
      sidebar.classList.remove("collapsed");
    } else {
      mobileMenuBtn.style.display = "none";
      sidebar.classList.remove("show");
    }
  }

  window.addEventListener("resize", handleResponsive);
  handleResponsive();
});

// Función global para mostrar modales
function showModal(modalHTML) {
  const modalContainer = document.getElementById("modal-container");
  modalContainer.innerHTML = `
    <div class="modal show">
      <div class="modal-content">
        ${modalHTML}
      </div>
    </div>
  `;

  // Agregar evento para cerrar modal
  modalContainer
    .querySelectorAll(".close-btn, .close-modal-btn")
    .forEach((btn) => {
      btn.addEventListener("click", closeModal);
    });
}

// Función global para cerrar modales
function closeModal() {
  const modalContainer = document.getElementById("modal-container");
  modalContainer.innerHTML = "";
}
