import React from 'react';
import ReactDOM from 'react-dom';
import { HashRouter, Route, Switch } from 'react-router-dom';

import NotFound from './admin/containers/NotFound';
import InviteAddonSetting from './admin/containers/subsite/codes/InviteAddonSetting'
import InviteAddonCreate from './admin/containers/subsite/codes/forms/CreateCode'
import InviteAddonEdit from './admin/containers/subsite/codes/forms/EditCode'

document.addEventListener('DOMContentLoaded', function () {
	const hammockContainer = document.getElementById( 'hammock-invites-container' );
	if ( hammockContainer !== null ) {
		const DefaultView = (props) => <NotFound hammock={window.hammock} {...props}/>
		const InvitesPage = (props) => <InviteAddonSetting hammock={window.hammock} {...props} />
		const InvitesCreatePage = (props) => <InviteAddonCreate type={`invitation`} hammock={window.hammock} {...props} />
		const InvitesEditPage = (props) => <InviteAddonEdit type={`invitation`} hammock={window.hammock} {...props} />
		const routing = (
			<HashRouter>
				<Switch>
					<Route exact path="/" component={InvitesPage} />
					<Route exact path="/new" component={InvitesCreatePage} />
					<Route exact path="/edit/:id" component={InvitesEditPage} />
					<Route component={DefaultView} />
				</Switch>
			</HashRouter>
		);
		ReactDOM.render(routing, hammockContainer);
	}
});