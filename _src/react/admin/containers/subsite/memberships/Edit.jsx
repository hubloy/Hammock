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
			api_url: this.props.hammock.api_url,
			api_nonce: this.props.hammock.api_nonce,
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
		var hammock = this.props.hammock;
		if ( this.state.loading ) {
			return (
				<div className="uk-container uk-padding-small uk-margin-top uk-width-1-1 uk-background-default">
					<span className="uk-text-center" uk-spinner="ratio: 3"></span>
				</div>
			)
		} else {
			if ( this.state.error) {
				return (
					<h3 className="uk-text-center uk-text-danger">{hammock.error}</h3>
				)
			} else {
				var membership = this.state.membership;
				var section = this.props.match.params.section !== undefined ? this.props.match.params.section : 'general';
				return (
					<Dashboard hammock={hammock} title={hammock.strings.edit.title}>
						{membership.id > 0 ? (
							<div className="hammock-settings uk-width-expand">
								<Nav hammock={hammock} active_nav={section} id={this.state.id}/>
								<div className="hammock-membership uk-background-default uk-padding-small">
									{
										{
											'price': <Price hammock={hammock} membership={membership}/>,
											'general': <General hammock={hammock} membership={membership}/>
										}[section]
									}
								</div>
							</div>
						) : (
							<h3 className="uk-text-center uk-text-danger">{hammock.strings.edit.not_found}</h3>
						) }
					</Dashboard>
				)
			}
		}
	}
}