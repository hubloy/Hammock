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
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
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
				this.notify( this.props.hammock.error, 'error' );
			}
		);
	}

	render() {
		var memberships = this.state.memberships;
		var hammock = this.props.hammock;
		return (
			<div className="uk-background-default uk-padding-small uk-panel uk-height-medium">
				{this.state.loading ? (
					<Preloader />
				) : (
					<React.Fragment>
						<p className="uk-h4">{hammock.strings.dashboard.memberships.title} <span className="hammock-badge-circle">{memberships.length}</span></p>
						<div>
							{memberships.length <= 0 ? (
								<Center text={hammock.strings.dashboard.memberships.none} className="uk-text-info" />
							) : (
								<ul className="uk-list">
									{memberships.map(item =>
										<li key={item.id}>
											<div className="uk-grid-collapse uk-child-width-expand@s" uk-grid="">
												<div>
													<a className="uk-text-primary" href={hammock.strings.dashboard.memberships.url + "#/edit/" + item.id} title={hammock.common.buttons.edit + ' ' + item.name}>{item.name}</a>
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