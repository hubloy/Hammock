import React from 'react';
import ReactDOM from 'react-dom';
import { HashRouter, Route, Switch } from 'react-router-dom';

import SiteComms from './admin/containers/subsite/Comms';
import SiteCommsManage from './admin/containers/subsite/comms/Manage';

document.addEventListener('DOMContentLoaded', function () {
	const hubloy_membershipContainer = document.getElementById( 'hubloy_membership-comms-container' );
	if ( hubloy_membershipContainer !== null ) {
		const CommsPage = (props) => <SiteComms hubloy_membership={window.hubloy_membership} {...props} />
		const CommsManagePage = (props) => <SiteCommsManage hubloy_membership={window.hubloy_membership} {...props} />
		const routing = (
			<HashRouter>
				<Switch>
					<Route exact path="/" component={CommsPage} />
					<Route path="/manage/:id" component={CommsManagePage} />
				</Switch>
			</HashRouter>
		);
		ReactDOM.render(routing, hubloy_membershipContainer);
	}
});