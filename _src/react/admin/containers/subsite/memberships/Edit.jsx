import React, { Component } from 'react';
import PropTypes from 'prop-types';

import fetchWP from '../../../../utils/fetchWP';
import Dashboard from '../layout/Dashboard';

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
			}), (err) => this.setState({ loading : false, error : true })
		);
	}

	render() {
		if ( this.state.loading ) {
			return (
				<div className="uk-container uk-padding-small uk-margin-top uk-width-1-1 uk-background-default">
					<span className="uk-text-center" uk-spinner="ratio: 3"></span>
				</div>
			)
		} else {
			if ( this.state.error) {
				return (
					<h3 className="uk-text-center uk-text-danger">{this.props.hammock.error}</h3>
				)
			} else {
				return (
					<Dashboard hammock={this.props.hammock}>
						{this.state.membership.id > 0 ? (
							<div uk-grid="">
								<Nav hammock={this.props.hammock}/>
								<div className="hammock-membership uk-width-expand uk-margin-left uk-card uk-card-body uk-switcher">
									<div className="uk-background-default uk-padding-small uk-border-rounded">
										<General hammock={this.props.hammock} membership={this.state.membership}/>
									</div>
									<div className="uk-background-default uk-padding-small uk-border-rounded">
										<Price hammock={this.props.hammock} membership={this.state.membership}/>
									</div>
								</div>
							</div>
						) : (
							<h3 className="uk-text-center uk-text-danger">{this.props.hammock.strings.edit.not_found}</h3>
						) }
					</Dashboard>
				)
			}
		}
	}
}