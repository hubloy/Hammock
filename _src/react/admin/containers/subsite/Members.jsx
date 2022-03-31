import React, { Component } from 'react';
import PropTypes from 'prop-types';

import Dashboard from 'layout/Dashboard'
import { Filter } from './members/Filter';
import Table from './members/Table';
import {Create} from './members/Create';
import fetchWP from 'utils/fetchWP'

export default class Members extends Component {
	constructor(props) {
		super(props);

		this.state = {
			total : 0,
			loading : true
		};
		
		this.fetchWP = new fetchWP({
			api_url: this.props.hubloy_membership.api_url,
			api_nonce: this.props.hubloy_membership.api_nonce,
        });
	}

	async componentDidMount() {
		this.count_members()
	}

	count_members = async () => {
        this.fetchWP.get( 'members/count' )
            .then( (json) => this.setState({
				total : typeof json.total !== 'undefined' ? json.total : 0,
				loading : false
            }), (err) => this.setState({ loading : false })
        );
    }

	render() {
		var strings = this.props.hubloy_membership.strings;
		return (
			<Dashboard hubloy_membership={this.props.hubloy_membership} button={<a className="uk-button uk-button-primary uk-button-small" href="#hubloy_membership-add-member" uk-toggle="">{strings.dashboard.add_new.button}</a>}>
				{!this.state.loading && this.state.total > 0 && 
					<Filter strings={strings}/>
				}
				<div className="uk-container uk-padding-small uk-margin-top uk-width-1-1 uk-background-default">
					<Table hubloy_membership={this.props.hubloy_membership} load_counts={this.count_members.bind(this)}/>
				</div>
				<Create hubloy_membership={this.props.hubloy_membership} />
			</Dashboard>
		)
	}
}

Members.propTypes = {
	hubloy_membership: PropTypes.object
};