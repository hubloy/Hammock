import React, { Component } from 'react';
import PropTypes from 'prop-types';
import LazyLoad from 'react-lazyload';

import fetchWP from '../../../../../utils/fetchWP';
import Dashboard from '../../layout/Dashboard';
import {PaginationUI} from '../../../../ui/admin/form';
import {Nav} from './Nav'

export default class MemberActivity extends Component {

	constructor(props) {
		super(props);

		this.state = {
			pager: {},
            items: [],
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
		this.loadPage();
	}

	loadPage = async () => {
        const page = parseInt( location.hash.split("/").pop() ) || 1;
        if ( page !== this.state.pager.current ) {
            this.fetchWP.get( 'activity/list?page=' + page + '&ref_id=' + this.state.id + '&ref_type=member' )
                .then( (json) => this.setState({
                    items : json.items,
                    pager : json.pager,
                    loading : false,
				    error : false,
                }), (err) => this.setState({ loading : false, error : true })
            );
        }
	}

	memberActivities() {
		const { pager, items } = this.state;
        var strings = this.props.hammock.strings;
		return (
			<Dashboard hammock={this.props.hammock}>
				<div uk-grid="">
					<LazyLoad className="uk-width-1-4 uk-height-medium">
						<Nav hammock={this.props.hammock} active_nav={'activity'} member_id={this.state.id}/>
					</LazyLoad>
					<div className="uk-width-expand uk-margin-left uk-card uk-card-body uk-background-default uk-padding-small">
						{items.length <= 0 ? (
                            <h3 className="uk-text-center">{strings.edit.activities.not_found}</h3>
                        ) : (
							<ul className="uk-list uk-list-striped">
								{items.map(item =>
									<li key={item.log_id}>
										<div className="uk-width-expand">
											<p className="uk-text-meta uk-margin-remove-bottom uk-text-small">
												<time datetime={item.date}>{item.date}</time>
											</p>
											<h6 className="uk-margin-remove-top uk-margin-remove-bottom">
												{item.action} {':'} <span dangerouslySetInnerHTML={{ __html: item.description }} /> {'('} <span className="uk-text-small" dangerouslySetInnerHTML={{ __html: item.author }} /> {')'}
											</h6>
										</div>
									</li>
								)}
							</ul>
						)}
						<PaginationUI pager={pager}/>
					</div>
				</div>
			</Dashboard>
		)
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
				return this.memberActivities();
			}
		}
	}
}

MemberActivity.propTypes = {
	hammock: PropTypes.object
};