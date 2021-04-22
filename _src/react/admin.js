import React from 'react';
import ReactDOM from 'react-dom';

import NetworkAdmin from './admin/containers/network/Admin';
import SiteAdmin from './admin/containers/subsite/Admin';

document.addEventListener('DOMContentLoaded', function () {
	const jengaPressContainer = document.getElementById( 'hammock-admin-container' );
	if ( jengaPressContainer !== null ) {
		if ( window.hammock.is_multisite ) {
			ReactDOM.render(<NetworkAdmin hammock={window.hammock} />, jengaPressContainer);
		} else {
			ReactDOM.render(<SiteAdmin hammock={window.hammock} />, jengaPressContainer);
		}
	}
});