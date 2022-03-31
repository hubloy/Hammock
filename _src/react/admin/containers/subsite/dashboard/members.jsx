import React, { Component } from 'react';

import fetchWP from 'utils/fetchWP';

import {Preloader, Center} from 'ui/admin/form';

import { toast } from 'react-toastify';

export default class MembersDashboard extends Component {

	constructor(props) {
		super(props);
		this.state = {
			loading : true,
			members : []
        };
        this.fetchWP = new fetchWP({
			api_url: this.props.hubloy_membership.api_url,
			api_nonce: this.props.hubloy_membership.api_nonce,
        });
	}


	notify(type, message) {
		toast[type](message, {toastId: 'dashboard-members-toast'});
	}

	componentDidMount() {
		this.load_data();
	}

	load_data = async() => {
		this.fetchWP.get( 'dashboard/members' )
			.then( (json) => this.setState({
				members : json,
				loading : false,
				error : false,
			}), (err) => {
				this.setState({ loading : false, error : true });
				this.notify( this.props.hubloy_membership.error, 'error' );
			}
		);
	}

	render() {
		var members = this.state.members;
		var hubloy_membership = this.props.hubloy_membership;
		return (
			<div className="uk-background-default uk-padding-small uk-panel uk-height-medium">
				{this.state.loading ? (
					<Preloader />
				) : (
					<React.Fragment>
						<p className="uk-h4">{hubloy_membership.strings.dashboard.members.title} <span className="hubloy_membership-badge-circle">{members.length}</span></p>
						<div>
							{members.length <= 0 ? (
								<Center text={hubloy_membership.strings.dashboard.members.none} className="uk-text-info" />
							) : (
								<ul className="uk-list">
									{members.map(item =>
										<li key={item.id}>
											<div className="uk-grid-collapse uk-child-width-expand@s" uk-grid="">
												<div>
													<a className="uk-text-primary" href={hubloy_membership.strings.dashboard.members.url + "#/member/" + item.id} title={hubloy_membership.common.buttons.edit + ' ' + item.user_info.name}>{item.user_info.name}</a>
												</div>
												<div className="uk-width-1-3 uk-text-right">{item.status}</div>
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