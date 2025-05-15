/**
* Template Main JS File
*/
document.addEventListener('DOMContentLoaded', () => {
  "use strict";

  /**
   * Toggle .header-scrolled class to #header when page is scrolled
   */
  const selectHeader = document.querySelector('#header');
  if (selectHeader) {
    document.addEventListener('scroll', () => {
      window.scrollY > 100 ? selectHeader.classList.add('header-scrolled') : selectHeader.classList.remove('header-scrolled');
    });
  }

  /**
   * Back to top button
   */
  const backtotop = document.querySelector('.back-to-top');
  if (backtotop) {
    const toggleBacktotop = () => {
      window.scrollY > 100 ? backtotop.classList.add('active') : backtotop.classList.remove('active');
    }
    window.addEventListener('load', toggleBacktotop);
    document.addEventListener('scroll', toggleBacktotop);
  }

  /**
   * Sidebar toggle
   */
  const toggleSidebar = document.querySelector('.toggle-sidebar-btn');
  const body = document.querySelector('body');
  if (toggleSidebar) {
    toggleSidebar.addEventListener('click', () => {
      body.classList.toggle('toggle-sidebar');
    });
  }

  /**
   * Search bar toggle
   */
  const searchBar = document.querySelector('.search-bar');
  const searchButton = document.querySelector('.search-bar-toggle');
  if (searchButton) {
    searchButton.addEventListener('click', () => {
      searchBar.classList.toggle('search-bar-show');
    });
  }

  /**
   * Initialize DataTables
   */
  const datatables = document.querySelectorAll('.datatable');
  datatables.forEach(datatable => {
    new simpleDatatables.DataTable(datatable, {
      perPageSelect: [5, 10, 15, 20, 25],
      columns: [{
        select: [3, 4], // Colonnes des badges (rôle et statut)
        sortable: false
      }]
    });
  });

  /**
   * Autoresize echart charts
   */
  const mainContainer = document.querySelector('#main');
  if (mainContainer) {
    setTimeout(() => {
      new ResizeObserver(function() {
        window.dispatchEvent(new Event('resize'));
      }).observe(mainContainer);
    }, 1000);
  }
}); 