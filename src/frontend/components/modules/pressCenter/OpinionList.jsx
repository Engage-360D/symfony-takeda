/**
 * cardiomagnyl
 */

var React = require('react');
var truncate = require('html-truncate');
var NextPageButton = require('./NextPageButton');
var PrevPageButton = require('./PrevPageButton');

var OpinionList = React.createClass({
  getInitialState: function() {
    return {
      isOrderedByViewsCount: false,
      page: 1
    };
  },

  getDefaultProps: function() {
    return {
      articlesPerPage: 4,
      paginationVisiblePageNum: 4
    };
  },

  getExpert: function(opinion) {
    // TODO ajax expert
    return opinion.links.expert;
  },

  getPageNum: function(opinions) {
    return Math.ceil(opinions.length / this.props.articlesPerPage);
  },

  renderOpinion: function(opinion) {
    var expert = this.getExpert(opinion);

    return (
      <a className="post__cell" href={opinion.uri}>
        <div className="post__man">
          <div className="post__img">
            <img src={expert.photoUri} alt="" />
            </div>
            <div className="post__data">
              <div className="post__name">{expert.name}</div>
              <div className="post__position">{expert.description}</div>
            </div>
          </div>
          <div className="post__title">{opinion.title}</div>
          <div className="post__text" dangerouslySetInnerHTML={{__html: truncate(opinion.content, 200)}} />
      </a>
    );
  },

  renderPager: function(opinions) {
    var pageNum = this.getPageNum(opinions);
    var currPage = this.state.page;
    var visibleNum = this.props.paginationVisiblePageNum;
    var links = [];
    var start = Math.floor((currPage - 1)/ visibleNum) * visibleNum + 1;
    var i;

    if (!pageNum) {
      return <div />
    }

    if (start > pageNum) {
      start = pageNum - visibleNum;
    }

    if (start > visibleNum) {
      links.push(<a href="#" onClick={this.showPage.bind(this, start - 1)}>...</a>);
    }

    for (i = start; i < start + visibleNum && i <= pageNum; i++) {
      links.push(
        <a href="#"
          onClick={this.showPage.bind(this, i)}
          className={i === currPage ? "is-active" : ""}>{i}</a>
      )
    }

    if (i <= pageNum) {
      links.push(<a href="#" onClick={this.showPage.bind(this, i)}>...</a>);
    }

    return (<div className="pager">{links}</div>);
  },

  showPage: function(page, e) {
    e.preventDefault();
    this.setState({
      page: page
    });
  },

  showPrevPage: function() {
    this.setState({
      page: Math.max(this.state.page - 1, 1)
    });
  },

  showNextPage: function(opinions) {
    this.setState({
      page: Math.min(this.state.page + 1, this.getPageNum(opinions))
    });
  },

  sortByViewsCount: function(e) {
    e.preventDefault();
    this.setState({isOrderedByViewsCount: true});
  },

  resetSorting: function(e) {
    e.preventDefault();
    this.setState({isOrderedByViewsCount: false});
  },

  render: function() {
    // initially opinions are sorted by createdAt field
    var opinions = this.props.opinions.slice();
    var firstRow = [];
    var secondRow = [];
    var i;

    if (this.state.isOrderedByViewsCount) {
      opinions.sort(function (a, b) {
        return b.viewsCount - a.viewsCount;
      });
    }

    for (i = (this.state.page - 1) * this.props.articlesPerPage; i < opinions.length; i++) {
      if (i % 2 === 0) {
        firstRow.push(opinions[i]);
      } else {
        secondRow.push(opinions[i]);
      }
    }

    return (
      <div>
        <div className="post">
          <div className="h-wrap">
            <div className="h">Мнения специалистов</div>
            <div className="sorter">
              <a href="#" onClick={this.sortByViewsCount} className={this.state.isOrderedByViewsCount ? "is-active" : ""}>самые популярные</a>
              <a href="#" onClick={this.resetSorting} className={!this.state.isOrderedByViewsCount ? "is-active" : ""}>новые</a>
            </div>
          </div>
          {this.state.page < this.getPageNum(opinions) ?
            <NextPageButton onClick={this.showNextPage.bind(this, opinions)} /> : <div/>
          }
          {this.state.page > 1 ?
            <PrevPageButton onClick={this.showPrevPage} /> : <div/>
          }
          <div className="post__table">
            <div className="post__row">{firstRow.map(this.renderOpinion)}</div>
            <div className="post__row post__row_down">{secondRow.map(this.renderOpinion)}</div>
          </div>
        </div>
        {this.renderPager.call(this, opinions)}
      </div>
    );
  }
});

module.exports = OpinionList;