import React from 'react';
import ReactDOM from 'react-dom';
import { HashRouter, Route, Switch } from 'react-router-dom';

import NotFound from './admin/containers/NotFound';
import SiteMembers from './admin/containers/subsite/Members';
import MemberEdit from './admin/containers/subsite/members/Edit';


document.addEventListener('DOMContentLoaded', function () {
	const hammockContainer = document.getElementById( 'hammock-members-container' );
	if ( hammockContainer !== null ) {
		const DefaultView = (props) => <NotFound hammock={window.hammock} {...props}/>
		const MembersPage = (props) => <SiteMembers hammock={window.hammock} {...props} />
		const MemberViewPage = (props) => <MemberEdit hammock={window.hammock} {...props} />
		const routing = (
			<HashRouter>
				<Switch>
					<Route exact path="/:page?" component={MembersPage} />
					<Route exact path="/member/:id/:section?/:page?" component={MemberViewPage} />
					<Route component={DefaultView} />
				</Switch>
			</HashRouter>
		);
		ReactDOM.render(routing, hammockContainer);
	}
});