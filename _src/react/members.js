import React from 'react';
import ReactDOM from 'react-dom';
import { HashRouter, Route, Switch } from 'react-router-dom';

import NotFound from './admin/containers/NotFound';
import SiteMembers from './admin/containers/subsite/Members';
import MemberDetail from './admin/containers/subsite/members/view/MemberDetail';
import MemberActivity from './admin/containers/subsite/members/view/MemberActivity';
import MemberTransactions from './admin/containers/subsite/members/view/MemberTransactions';


document.addEventListener('DOMContentLoaded', function () {
	const hammockContainer = document.getElementById( 'hammock-members-container' );
	if ( hammockContainer !== null ) {
		const DefaultView = (props) => <NotFound hammock={window.hammock} {...props}/>
		const MembersPage = (props) => <SiteMembers hammock={window.hammock} {...props} />
		const MemberViewPage = (props) => <MemberDetail hammock={window.hammock} {...props} />
		const MemberViewActivityPage = (props) => <MemberActivity hammock={window.hammock} {...props} />
		const MemberViewTransactionsPage = (props) => <MemberTransactions hammock={window.hammock} {...props} />
		const routing = (
			<HashRouter>
				<Switch>
					<Route exact path="/" component={MembersPage} />
					<Route path="/paged/:page" component={MembersPage} />
					<Route exact path="/member/:id" component={MemberViewPage} />
					<Route exact path="/member/:id/activity" component={MemberViewActivityPage} />
					<Route exact path="/member/:id/activity/paged/:page" component={MemberViewActivityPage} />
					<Route exact path="/member/:id/transactions" component={MemberViewTransactionsPage} />
					<Route exact path="/member/:id/transactions/paged/:page" component={MemberViewTransactionsPage} />
					<Route component={DefaultView} />
				</Switch>
			</HashRouter>
		);
		ReactDOM.render(routing, hammockContainer);
	}
});