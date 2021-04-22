import React from 'react';
import ReactDOM from 'react-dom';
import { HashRouter, Route, Switch } from 'react-router-dom';

import NotFound from './admin/containers/NotFound';
import SiteSettings from './admin/containers/subsite/Settings';
import GatewaySettings from './admin/containers/subsite/settings/GatewaySetting';

document.addEventListener('DOMContentLoaded', function () {
	const jengaPressContainer = document.getElementById( 'hammock-settings-container' );
	if ( jengaPressContainer !== null ) {
		const DefaultView = (props) => <NotFound hammock={window.hammock} {...props}/>
		const SettingsPage = (props) => <SiteSettings hammock={window.hammock} {...props}/>
		const GatewaySettingsPage = (props) => <GatewaySettings hammock={window.hammock} {...props} />
		const routing = (
			<HashRouter>
				<Switch>
					<Route exact path="/" component={SettingsPage} />
					<Route exact path="/gateways" component={GatewaySettingsPage} />
					<Route component={DefaultView} />
				</Switch>
			</HashRouter>
		);
		ReactDOM.render(routing, jengaPressContainer);
	}
});