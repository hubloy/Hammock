import React from 'react';
import ReactDOM from 'react-dom';

import SiteAddons from './admin/containers/subsite/Addon';

document.addEventListener('DOMContentLoaded', function () {
	const hammockContainer = document.getElementById( 'hammock-addon-container' );
	if ( hammockContainer !== null ) {
		ReactDOM.render(<SiteAddons hammock={window.hammock} />, hammockContainer);
	}
});