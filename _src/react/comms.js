import React from 'react';
import ReactDOM from 'react-dom';
import { HashRouter, Route, Switch } from 'react-router-dom';

import SiteComms from './admin/containers/subsite/Comms';
import SiteCommsManage from './admin/containers/subsite/comms/Manage';

document.addEventListener('DOMContentLoaded', function () {
	const jengaPressContainer = document.getElementById( 'hammock-comms-container' );
	if ( jengaPressContainer !== null ) {
		const CommsPage = (props) => <SiteComms hammock={window.hammock} {...props} />
		const CommsManagePage = (props) => <SiteCommsManage hammock={window.hammock} {...props} />
		const routing = (
			<HashRouter>
				<Switch>
					<Route exact path="/" component={CommsPage} />
					<Route path="/manage/:id" component={CommsManagePage} />
				</Switch>
			</HashRouter>
		);
		ReactDOM.render(routing, jengaPressContainer);
	}
});