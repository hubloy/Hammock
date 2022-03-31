import React from 'react';
import ReactDOM from 'react-dom';
import { HashRouter, Route, Switch } from 'react-router-dom';
import SiteTransactions from './admin/containers/subsite/Transactions';
import AddTransaction from './admin/containers/subsite/transactions/Add';
import EditTransaction from './admin/containers/subsite/transactions/Edit';

document.addEventListener('DOMContentLoaded', function () {
	const hubloy_membershipContainer = document.getElementById( 'hubloy_membership-transactions-container' );
	if ( hubloy_membershipContainer !== null ) {
		const TransactionsPage = (props) => <SiteTransactions hubloy_membership={window.hubloy_membership} {...props} />
		const AddTransactionsPage = (props) => <AddTransaction hubloy_membership={window.hubloy_membership} {...props} />
		const EditTransactionPage = (props) => <EditTransaction hubloy_membership={window.hubloy_membership} {...props} />
		const routing = (
			<HashRouter>
				<Switch>
					<Route exact path="/" component={TransactionsPage} />
					<Route exact path="/page/:page?" component={TransactionsPage} />
					<Route exact path="/add" component={AddTransactionsPage} />
					<Route exact path="/transaction/:id" component={EditTransactionPage} />
				</Switch>
			</HashRouter>
		);
		ReactDOM.render(routing, hubloy_membershipContainer);
	}
});