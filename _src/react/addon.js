import React from 'react';
import ReactDOM from 'react-dom';

import SiteAddons from './admin/containers/subsite/Addon';

document.addEventListener('DOMContentLoaded', function () {
	const jengaPressContainer = document.getElementById( 'hammock-addon-container' );
	if ( jengaPressContainer !== null ) {
		ReactDOM.render(<SiteAddons hammock={window.hammock} />, jengaPressContainer);
	}
});