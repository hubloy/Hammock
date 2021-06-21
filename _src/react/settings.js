import React from 'react';
import ReactDOM from 'react-dom';
import { HashRouter, Route, Switch } from 'react-router-dom';

import NotFound from './admin/containers/NotFound';
import SiteSettings from './admin/containers/subsite/Settings';

document.addEventListener('DOMContentLoaded', function () {
	const hammockContainer = document.getElementById( 'hammock-settings-container' );
	if ( hammockContainer !== null ) {
		const DefaultView = (props) => <NotFound hammock={window.hammock} {...props}/>
		const SettingsPage = (props) => <SiteSettings hammock={window.hammock} {...props}/>
		const GatewaySettingsPage = (props) => <GatewaySettings hammock={window.hammock} {...props} />
		const routing = (
			<HashRouter>
				<Switch>
					<Route exact path="/:section?" component={SettingsPage} />
					<Route component={DefaultView} />
				</Switch>
			</HashRouter>
		);
		ReactDOM.render(routing, hammockContainer);
	}
});