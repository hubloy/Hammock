import React from 'react';
import ReactDOM from 'react-dom';
import { HashRouter, Route, Switch } from 'react-router-dom';

import SiteMarketing from './admin/containers/subsite/Marketing';

document.addEventListener('DOMContentLoaded', function () {
	const hammockContainer = document.getElementById( 'hammock-marketing-container' );
	if ( hammockContainer !== null ) {
		const SiteMarketingPage = (props) => <SiteMarketing hammock={window.hammock} {...props} />
		const routing = (
			<HashRouter>
				<Switch>
					<Route exact path="/:page?" component={SiteMarketingPage} />
				</Switch>
			</HashRouter>
		);
		ReactDOM.render(routing, hammockContainer);
	}
});