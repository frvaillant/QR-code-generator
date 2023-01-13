
    ( function( wp ) {
        wp.data.dispatch('core/notices').createErrorNotice(
            atob(my_routes_errors.error_message),
            {
                isDismissible: true,
                __unstableHTML: true
            }
        );
    } )( window.wp );




