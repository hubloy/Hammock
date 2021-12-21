import React from 'react';
import ReactDOM from 'react-dom';
import { HashRouter, Route, Switch } from 'react-router-dom';

import SiteRules from './admin/containers/subsite/Rules';

document.addEventListener('DOMContentLoaded', function () {
	const hammockContainer = document.getElementById( 'hammock-rules-container' );
	if ( hammockContainer !== null ) {
		const RulesPage = (props) => <SiteRules hammock={window.hammock} {...props} />
		const routing = (
			<HashRouter>
				<Switch>
					<Route exact path="/:section?" component={RulesPage} />
					<Route exact path="/:section?/page/:page?" component={RulesPage} />
				</Switch>
			</HashRouter>
		);
		ReactDOM.render(routing, hammockContainer);
	}
});