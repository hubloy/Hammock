import React, { Component } from 'react';
import PropTypes from 'prop-types';

import Dashboard from 'layout/Dashboard'
import { Filter } from './transactions/Filter';
import Table from './transactions/Table';

export default class Transactions extends Component {
	constructor(props) {
		super(props);
	}

	render() {
		var hammock = this.props.hammock;
		return (
			<Dashboard hammock={hammock}>
				<Filter hammock={hammock} />
				<div className="uk-container uk-padding-small uk-margin-top uk-width-1-1">
					<Table hammock={this.props.hammock} />
				</div>
			</Dashboard>
		)
	}
}

Transactions.propTypes = {
	hammock: PropTypes.object
};