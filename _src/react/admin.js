import React from 'react';
import ReactDOM from 'react-dom';

import NetworkAdmin from './admin/containers/network/Admin';
import SiteAdmin from './admin/containers/subsite/Admin';

document.addEventListener('DOMContentLoaded', function () {
	const hammockContainer = document.getElementById( 'hammock-admin-container' );
	if ( hammockContainer !== null ) {
		if ( window.hammock.is_multisite ) {
			ReactDOM.render(<NetworkAdmin hammock={window.hammock} />, hammockContainer);
		} else {
			ReactDOM.render(<SiteAdmin hammock={window.hammock} />, hammockContainer);
		}
	}
});