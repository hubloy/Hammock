import React, { Component } from 'react';

import fetchWP from 'utils/fetchWP';
import Dashboard from 'layout/Dashboard';

import { Nav } from './edit/Nav'
import General from './edit/General'
import Price from './edit/Price'

export default class Edit extends Component {

	constructor(props) {
		super(props);
		
		this.state = {
			membership : {},
			id : this.props.match.params.id,
			loading : true,
			error : false
        };
        this.fetchWP = new fetchWP({
			api_url: this.props.hubloy_membership.api_url,
			api_nonce: this.props.hubloy_membership.api_nonce,
        });
	}
	

	async componentDidMount() {
		const id = this.state.id;
		this.fetchWP.get( 'memberships/get?id=' + id )
			.then( (json) => this.setState({
				membership : json,
				loading : false,
				error : false,
			}), (err) => {
				this.setState({ loading : false, error : true });
			}
		);
	}

	render() {
		var hubloy_membership = this.props.hubloy_membership;
		if ( this.state.loading ) {
			return (
				<div className="uk-container uk-padding-small uk-margin-top uk-width-1-1 uk-background-default">
					<span className="uk-text-center" uk-spinner="ratio: 3"></span>
				</div>
			)
		} else {
			if ( this.state.error) {
				return (
					<h3 className="uk-text-center uk-text-danger">{hubloy_membership.error}</h3>
				)
			} else {
				var membership = this.state.membership;
				var section = this.props.match.params.section !== undefined ? this.props.match.params.section : 'general';
				return (
					<Dashboard hubloy_membership={hubloy_membership} title={hubloy_membership.strings.edit.title}>
						{membership.id > 0 ? (
							<div className="hubloy_membership-settings uk-width-expand">
								<Nav hubloy_membership={hubloy_membership} active_nav={section} id={this.state.id}/>
								<div className="hubloy_membership-membership uk-background-default uk-padding-small">
									{
										{
											'price': <Price hubloy_membership={hubloy_membership} membership={membership}/>,
											'general': <General hubloy_membership={hubloy_membership} membership={membership}/>
										}[section]
									}
								</div>
							</div>
						) : (
							<h3 className="uk-text-center uk-text-danger">{hubloy_membership.strings.edit.not_found}</h3>
						) }
					</Dashboard>
				)
			}
		}
	}
}