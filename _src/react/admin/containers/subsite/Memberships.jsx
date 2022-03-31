import React, { Component } from 'react';
import PropTypes from 'prop-types';

import Dashboard from 'layout/Dashboard';
import { Filter } from './memberships/Filter';
import Table from './memberships/Table';
import CreateMembership from './memberships/Create';
import fetchWP from 'utils/fetchWP'

import { toast } from 'react-toastify';

export default class Memberships extends Component {
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
		this.count_memberships();
	}

	notify(type, message) {
		toast[type](message, {toastId: 'memberships-toast'});
	}
	
	count_memberships = async () => {
        this.fetchWP.get( 'memberships/count' )
            .then( (json) => this.setState({
				total : typeof json.total !== 'undefined' ? json.total : 0,
				loading : false
            }), (err) => {
				this.notify( this.props.hubloy_membership.error, 'error' );
				this.setState({loading : false});
			}
        );
    }

	render() {
		var strings = this.props.hubloy_membership.strings;
		return (
			<Dashboard hubloy_membership={this.props.hubloy_membership} button={<a className="uk-button uk-button-primary uk-button-small" href="#hubloy_membership-add-membership" uk-toggle="">{strings.dashboard.add_new.button}</a>}>
				{!this.state.loading && this.state.total > 0 && 
					<Filter strings={strings}/>
				}
				<div className="uk-container uk-padding-small uk-margin-top uk-width-1-1 uk-background-default">
					<Table hubloy_membership={this.props.hubloy_membership} />
				</div>
				<CreateMembership hubloy_membership={this.props.hubloy_membership}/>
				
			</Dashboard>
		)
	}
}

Memberships.propTypes = {
	hubloy_membership: PropTypes.object
};