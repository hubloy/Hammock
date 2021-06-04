import React, { Component } from 'react';
import PropTypes from 'prop-types';

import Dashboard from 'layout/Dashboard';
import { Filter } from './memberships/Filter';
import Table from './memberships/Table';
import CreateMembership from './memberships/Create';
import fetchWP from 'utils/fetchWP'

export default class Memberships extends Component {
	constructor(props) {
		super(props);

		this.state = {
			total : 0,
			loading : true
		};
		
		this.fetchWP = new fetchWP({
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
        });
	}

	async componentDidMount() {
		this.count_memberships();
	}
	
	count_memberships = async () => {
        this.fetchWP.get( 'memberships/count' )
            .then( (json) => this.setState({
				total : typeof json.total !== 'undefined' ? json.total : 0,
				loading : false
            }), (err) => console.log( 'error', err )
        );
    }

	render() {
		var strings = this.props.hammock.strings;
		return (
			<Dashboard hammock={this.props.hammock}>
				<h2 className="uk-text-center uk-heading-divider">{this.props.hammock.common.string.title}</h2>
				{!this.state.loading && this.state.total > 0 && 
					<a className="uk-button uk-button-primary uk-button-small" href="#hammock-add-membership" uk-toggle="">{strings.dashboard.add_new.button}</a>
				}
				{!this.state.loading && this.state.total > 0 && 
					<Filter strings={strings}/>
				}
				<div className="uk-container uk-padding-small uk-margin-top uk-width-1-1 uk-background-default">
					<Table hammock={this.props.hammock} />
				</div>
				<CreateMembership hammock={this.props.hammock}/>
				
			</Dashboard>
		)
	}
}

Memberships.propTypes = {
	hammock: PropTypes.object
};