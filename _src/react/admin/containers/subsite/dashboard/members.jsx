import React, { Component } from 'react';

import fetchWP from 'utils/fetchWP';

import {Preloader, Center} from 'ui/admin/form';

export default class MembersDashboard extends Component {

	constructor(props) {
		super(props);
		this.state = {
			loading : true,
			members : []
        };
        this.fetchWP = new fetchWP({
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
        });
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
			}), (err) => this.setState({ loading : false, error : true })
		);
	}

	render() {
		var members = this.state.members;
		var hammock = this.props.hammock;
		return (
			<div className="uk-background-default uk-padding-small uk-panel uk-height-medium">
				{this.state.loading ? (
					<Preloader />
				) : (
					<React.Fragment>
						<p className="uk-h4">{hammock.strings.members.title} <span className="hammock-badge-circle">{members.length}</span></p>
						<div>
							{members.length <= 0 ? (
								<Center text={hammock.strings.members.none} className="uk-text-info" />
							) : (
								<ul className="uk-list uk-list-striped">
									{members.map(item =>
										<li key={item.id}><a className="uk-text-primary" href={hammock.strings.members.url + "#/member/" + item.id}>{item.user_info.name}</a></li>
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