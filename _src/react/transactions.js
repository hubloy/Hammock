import React from 'react';
import ReactDOM from 'react-dom';
import { HashRouter, Route, Switch } from 'react-router-dom';
import SiteTransactions from './admin/containers/subsite/Transactions';
import AddTransaction from './admin/containers/subsite/transactions/Add';
import EditTransaction from './admin/containers/subsite/transactions/Edit';

document.addEventListener('DOMContentLoaded', function () {
	const hammockContainer = document.getElementById( 'hammock-transactions-container' );
	if ( hammockContainer !== null ) {
		const TransactionsPage = (props) => <SiteTransactions hammock={window.hammock} {...props} />
		const AddTransactionsPage = (props) => <AddTransaction hammock={window.hammock} {...props} />
		const EditTransactionPage = (props) => <EditTransaction hammock={window.hammock} {...props} />
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
		ReactDOM.render(routing, hammockContainer);
	}
});