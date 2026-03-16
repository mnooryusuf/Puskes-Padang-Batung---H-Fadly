<style>
    /* Premium Dashboard Enhancements */
    
    /* 1. Glassmorphism for Sidebar */
    .fi-sidebar {
        background: rgba(255, 255, 255, 0.8) !important;
        backdrop-filter: blur(12px) !important;
        border-right: 1px solid rgba(0, 0, 0, 0.05) !important;
    }
    
    .dark .fi-sidebar {
        background: rgba(24, 24, 27, 0.8) !important;
        backdrop-filter: blur(12px) !important;
        border-right: 1px solid rgba(255, 255, 255, 0.05) !important;
    }
    
    /* 2. Soft Shadows & Hover Effects for Cards */
    .fi-section, .fi-wi-stats-overview-stat-card {
        border: none !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03) !important;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    }
    
    .fi-section:hover, .fi-wi-stats-overview-stat-card:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.07), 0 4px 6px -2px rgba(0, 0, 0, 0.04) !important;
    }
    
    /* 3. Modern Scrollbar */
    ::-webkit-scrollbar {
        width: 6px;
        height: 6px;
    }
    
    ::-webkit-scrollbar-track {
        background: transparent;
    }
    
    ::-webkit-scrollbar-thumb {
        background: rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }
    
    .dark ::-webkit-scrollbar-thumb {
        background: rgba(255, 255, 255, 0.1);
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: rgba(0, 0, 0, 0.2);
    }
    
    /* 4. Smooth Micro-animations for Buttons */
    .fi-btn {
        transition: all 0.2s ease !important;
    }
    
    .fi-btn:active {
        transform: scale(0.96) !important;
    }
    
    /* 5. Refined Header */
    .fi-topbar {
        background: rgba(255, 255, 255, 0.7) !important;
        backdrop-filter: blur(8px) !important;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05) !important;
    }
    
    .dark .fi-topbar {
        background: rgba(9, 9, 11, 0.7) !important;
        backdrop-filter: blur(8px) !important;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05) !important;
    }

    /* 6. Navigation Link Refinement */
    .fi-sidebar-item-button {
        border-radius: 8px !important;
        margin: 2px 8px !important;
        transition: all 0.2s ease !important;
    }
    
    .fi-sidebar-active.fi-sidebar-item-button {
        background-color: var(--primary-50) !important;
        color: var(--primary-600) !important;
    }
    
    .dark .fi-sidebar-active.fi-sidebar-item-button {
        background-color: rgba(20, 184, 166, 0.1) !important;
        color: var(--primary-400) !important;
    }
</style>
