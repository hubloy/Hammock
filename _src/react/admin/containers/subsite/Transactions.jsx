import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';
import Dashboard from 'layout/Dashboard'
import { Filter } from './transactions/Filter';
import Table from './transactions/Table';

export default class Transactions extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		var hubloy_membership = this.props.hubloy_membership;
		var page = this.props.match.params.page !== undefined ? this.props.match.params.page : 0;
		return (
			<Dashboard hubloy_membership={hubloy_membership} button={<Link className="uk-button uk-button-primary uk-button-small" to={"/add"}>{hubloy_membership.strings.dashboard.add_new.button}</Link>}>
				<Filter hubloy_membership={hubloy_membership} />
				<div className="uk-container uk-padding-small uk-margin-top uk-width-1-1">
					<Table hubloy_membership={this.props.hubloy_membership} page={page}/>
				</div>
			</Dashboard>
		)
	}
}

Transactions.propTypes = {
	hubloy_membership: PropTypes.object
};