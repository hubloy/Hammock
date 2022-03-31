import React, { Component } from 'react';
import fetchWP from 'utils/fetchWP';

import {Preloader, Center} from 'ui/admin/form';

import { toast } from 'react-toastify';

export default class MembershipDashboard extends Component {

	constructor(props) {
		super(props);
		this.state = {
			loading : true,
			memberships : []
        };
        this.fetchWP = new fetchWP({
			api_url: this.props.hubloy_membership.api_url,
			api_nonce: this.props.hubloy_membership.api_nonce,
        });
	}

	componentDidMount() {
		this.load_data();
	}

	notify(type, message) {
		toast[type](message, {toastId: 'dashboard-membership-toast'});
	}

	load_data = async() => {
		this.fetchWP.get( 'dashboard/memberships' )
			.then( (json) => this.setState({
				memberships : json,
				loading : false,
				error : false,
			}), (err) => {
				this.setState({ loading : false, error : true });
				this.notify( this.props.hubloy_membership.error, 'error' );
			}
		);
	}

	render() {
		var memberships = this.state.memberships;
		var hubloy_membership = this.props.hubloy_membership;
		return (
			<div className="uk-background-default uk-padding-small uk-panel uk-height-medium">
				{this.state.loading ? (
					<Preloader />
				) : (
					<React.Fragment>
						<p className="uk-h4">{hubloy_membership.strings.dashboard.memberships.title} <span className="hubloy_membership-badge-circle">{memberships.length}</span></p>
						<div>
							{memberships.length <= 0 ? (
								<Center text={hubloy_membership.strings.dashboard.memberships.none} className="uk-text-info" />
							) : (
								<ul className="uk-list">
									{memberships.map(item =>
										<li key={item.id}>
											<div className="uk-grid-collapse uk-child-width-expand@s" uk-grid="">
												<div>
													<a className="uk-text-primary" href={hubloy_membership.strings.dashboard.memberships.url + "#/edit/" + item.id} title={hubloy_membership.common.buttons.edit + ' ' + item.name}>{item.name}</a>
												</div>
												<div className="uk-width-1-3 uk-text-right">{item.enabled}</div>
											</div>
										</li>
									)}
								</ul>
							)}
						</div>
					</React.Fragment>
				)}
				
			</div>
		)
	}
}