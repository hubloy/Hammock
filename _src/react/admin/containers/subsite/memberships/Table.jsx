import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';
import fetchWP from 'utils/fetchWP';
import {PaginationUI, ToggleInfoBox} from 'ui/admin/form';

import { toast } from 'react-toastify';

export default class Table extends Component {

    constructor(props) {
        super(props);
		this.state = {
            pager: {},
            items: [],
			loading : true,
			error : false,
        };

        this.fetchWP = new fetchWP({
			api_url: this.props.hubloy_membership.api_url,
			api_nonce: this.props.hubloy_membership.api_nonce,
        });
    }

	notify(type, message) {
		toast[type](message, {toastId: 'memberships-table-toast'});
	}
    
    async componentDidMount() {
        this.loadPage();
    }

    async componentDidUpdate() {
		if ( typeof this.state.pager.current !== 'undefined' ) {
			this.loadPage();
		}
    }

    loadPage = async () => {
        const page = parseInt( location.hash.split("/").pop() ) || 1;
        if ( page !== this.state.pager.current ) {
            this.fetchWP.get( 'memberships/list?page=' + page )
                .then( (json) => this.setState({
                    items : json.items,
                    pager : json.pager,
					loading : false,
					error : false,
                }), (err) => {
					this.setState({ loading : false, error : true });
				}
            );
        }
    }


    render() {
        const { pager, items } = this.state;
        var hubloy_membership = this.props.hubloy_membership;
        var strings = hubloy_membership.strings;
        if ( this.state.loading) {
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
				return (
					<React.Fragment>
						{items.length <= 0 ? (
							<ToggleInfoBox title={strings.dashboard.table.not_found} icon={'album'} linkText={strings.dashboard.add_new.button} linkTo={'#hubloy_membership-add-membership'}/>
						) : (
							<table className="uk-table">
								<thead>
									<tr>
										<th><input className="uk-checkbox hubloy_membership-top-checkbox" type="checkbox" /></th>
										<th>{strings.dashboard.table.name}</th>
										<th>{strings.dashboard.table.active}</th>
										<th>{strings.dashboard.table.members}</th>
										<th>{strings.dashboard.table.price}</th>
										<th></th>
									</tr>
								</thead>
								<tfoot>
									<tr>
										<th><input className="uk-checkbox hubloy_membership-bottom-checkbox" type="checkbox" /></th>
										<th>{strings.dashboard.table.name}</th>
										<th>{strings.dashboard.table.active}</th>
										<th>{strings.dashboard.table.members}</th>
										<th>{strings.dashboard.table.price}</th>
										<th></th>
									</tr>
								</tfoot>
								<tbody>
									{items.map(item =>
										<tr key={item.id}>
											<td><input className="uk-checkbox" type="checkbox" value={item.id} /></td>
											<td>
												{item.name}
												<div id={"membership-hover-"+ item.id}>
													<Link className="uk-text-primary" to={"/edit/" + item.id}>{hubloy_membership.common.buttons.edit}</Link>{' '}|{' '}<a href="#" className="uk-text-danger">{hubloy_membership.common.buttons.delete}</a>
												</div>
											</td>
											<td>{item.enabled}</td>
											<td>{item.members}</td>
											<td><span dangerouslySetInnerHTML={{ __html: item.price_name }}></span></td>
											<td>
												<div className="uk-inline">
													<button className="uk-button uk-button-default uk-button-small uk-padding-small" type="button"><span uk-icon="code"></span></button>
													<div className='uk-padding-small' uk-dropdown="pos: bottom-right">
														<ul className="uk-nav uk-dropdown-nav hubloy_membership-shortcodes">
															<li className="uk-nav-header"><code>[hubloy_membership_single_membership id="{item.id}"]</code></li>
															<li className="uk-nav-header"><code>[hubloy_membership_membership_button id="{item.id}"]</code></li>
														</ul>
													</div>
												</div>
											</td>
										</tr>
									)}
								</tbody>
							</table>
						)}
						<PaginationUI pager={pager}/>
					</React.Fragment>
				)
			}
        }
    }
}