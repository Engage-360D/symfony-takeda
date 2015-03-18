/**
 * Engage-360D Sberbank Depository Frontend
 */

// Localization
var i18n = require("attreactive-i18n");
i18n.locale('ru');

// Rendering dependencies
var Promise = require('bluebird');
var React = require("react");
var Admin = require("engage-360d-admin/components/Admin");
var $ = require("jquery");

// Configuration dependencies
var AuthorizationManager = require("attreactive-auth/lib/AuthorizationManager");
var Router = require("attreactive-router/lib/Router");
var menuFactory = require("attreactive-menu/lib/menuFactory");
var AdminSetup = require("attreactive-admin/lib/AdminSetup");
var FormComponentMatcher = require("attreactive-admin/lib/FormComponentMatcher");
var FormatterFactory = require("attreactive-admin/lib/formatter/FormatterFactory");
var AdminResourceFactory = require("attreactive-admin/lib/resource/AdminResourceFactory");
var AdminResource = require("attreactive-admin/lib/resource/AdminResource");
var AdminResourceProperty = require("attreactive-admin/lib/resource/AdminResourceProperty");
var RestStorage = require("attreactive-admin/lib/storage/RestStorage");
var PropertyStringifier = require("attreactive-admin/lib/stringifier/PropertyStringifier");
var validationConstraints = require("attreactive-validator/lib/validationConstraints");
var PageBlockRegistry = require("engage-360d-admin/PageBlockRegistry");
var apiRequest = require("./utilities/apiRequest");

// Services
var authManager = new AuthorizationManager(
  function (credentials) {
    return Promise.resolve(apiRequest('POST', '/api/v1/tokens', {
      email: credentials._username,
      plainPassword: credentials._password
    }, function () {}))
      .then(function(output) {
        var token = output.data.id;
        var user = output.linked.users[0];
        return {
          userFullName: [user.firstname, user.lastname].join(' '),
          token: token
        };
      });
  }
);
var router = new Router();
var adminSetup = new AdminSetup(authManager, router);
var formComponentMatcher = new FormComponentMatcher();
var formatterFactory = new FormatterFactory();
var adminResourceFactory = new AdminResourceFactory(adminSetup, formComponentMatcher, formatterFactory);
var pageBlockRegistry = new PageBlockRegistry();

// Default CRUD components
adminResourceFactory.setDefaultListingComponent(require("engage-360d-admin/components/crud/CrudList"));
adminResourceFactory.setDefaultViewComponent(require("engage-360d-admin/components/crud/CrudView"));
adminResourceFactory.setDefaultFormComponent(require("engage-360d-admin/components/crud/CrudForm"));

// Form fields
formComponentMatcher.registerComponent('input', require("engage-360d-admin/components/form/Input"));
formComponentMatcher.registerComponent('textarea', require("engage-360d-admin/components/form/Textarea"));
formComponentMatcher.registerComponent('number-input', require("engage-360d-admin/components/form/NumberInput"));
formComponentMatcher.registerComponent('checkbox', require("engage-360d-admin/components/form/Checkbox"));
formComponentMatcher.registerComponent('password', require("engage-360d-admin/components/form/PasswordInput"));
formComponentMatcher.registerComponent('file', require("engage-360d-admin/components/form/FileInput"));
formComponentMatcher.registerComponent('many-to-many-select', require("engage-360d-admin/components/form/ManyToManySelect"));
formComponentMatcher.registerComponent('many-to-one-select', require("engage-360d-admin/components/form/ManyToOneSelect"));
formComponentMatcher.registerComponent('ckeditor', require("engage-360d-admin/components/form/CKEditor"));
formComponentMatcher.registerComponent('date-time-picker', require("engage-360d-admin/components/form/DateTimePicker"));

// Page Blocks
pageBlockRegistry.addPageBlockComponent({
  type: 'sonata.block.service.text',
  title: 'Текстовый блок',
  initialJSON: JSON.stringify({content: ''}),
  component: require("engage-360d-admin/components/pageBlocks/TextPageBlock")
});

pageBlockRegistry.addPageBlockComponent({
  type: 'engage360d_takeda.block.map',
  title: 'Карта',
  initialJSON: JSON.stringify({}),
  component: require("./admin/MapBlock")
});

pageBlockRegistry.addPageBlockComponent({
  type: 'engage360d_takeda.block.good_to_know',
  title: 'Полезно знать',
  initialJSON: JSON.stringify({
    color: '',
    image: '',
    title: '',
    content: ''
  }),
  component: require("./admin/GoodToKnowBlock")
});

// Formatters
formatterFactory.factory('string', require("attreactive-admin/lib/formatter/StringFormatter"));
formatterFactory.factory('number', require("attreactive-admin/lib/formatter/NumberFormatter"));
formatterFactory.factory('date', require("attreactive-admin/lib/formatter/DateFormatter"));
formatterFactory.factory('boolean', require("engage-360d-admin/lib/admin/formatter/BooleanFormatter"));
formatterFactory.factory('many-to-many', require("attreactive-admin/lib/formatter/ManyToManyFormatter"));
formatterFactory.factory('many-to-one', require("attreactive-admin/lib/formatter/ManyToOneFormatter"));

router.add('/', function() {
  window.location.href = '#!/pages/';
});

// Menu
var menu = [
  {title: 'Содержимое сайта', menu: menuFactory([
    {url: '/pages/', title: 'Страницы', icon: 'file-text-o'},
    {url: '/regions/', title: 'Регионы', icon: 'list'},
    {url: '/users/', title: 'Пользователи', icon: 'users'}
  ])},
  {title: 'Новости', menu: menuFactory([
    {url: '/news-categories/', title: 'Типы новостей', icon: 'tags'},
    {url: '/news/', title: 'Новости', icon: 'list'},
  ])},
  {title: 'Мнения экспертов', menu: menuFactory([
    {url: '/experts/', title: 'Эксперты', icon: 'tags'},
    {url: '/opinions/', title: 'Мнения', icon: 'list'},
  ])}
];

$(document).ajaxComplete(function(event, jqXHR, ajaxOptions) {
  if (jqXHR.status == 401) {
    localStorage.removeItem('attreactive-auth/payload');
    window.location.href = window.location.pathname;
  }
});

var catalogs = [
  {
    id: 'NewsCategories',
    factoryId: 'news-categories',
    name: 'Типы новостей',
    icon: 'tags',
    keyword: {
      isVisible: false
    }
  }
];

catalogs.forEach(function (catalog) {
  adminResourceFactory.factory(catalog.factoryId, {
    title: catalog.name,
    rest: {
      baseUrl: '/api/v1/records',
      defaultQuery: {
        catalogId: catalog.id
      },
      defaultData: {
        catalog: catalog.id
      }
    },
    stringify: 'data',
    meta: {
      icon: catalog.icon
    },
    properties: {
      data: {
        visibleInForm: false,
        title: 'Название',
        constraints: {
          notEmpty: validationConstraints.notEmpty()
        },
        errorMessages: {
          notEmpty: 'Название не может быть пустым'
        },
        // linkable: true
      },
      order: {
        visibleInForm: false,
        title: 'Общий порядковый номер',
        format: 'number',
        constraints: {
          notEmpty: validationConstraints.notEmpty()
        },
        errorMessages: {
          notEmpty: 'Общий порядковый номер не может быть пустым'
        }
      },
      keyword: {
        title: catalog.keyword.title,
        constraints: {
          notEmpty: validationConstraints.notEmpty()
        },
        errorMessages: {
          notEmpty: catalog.keyword.title + ' не может быть пустым'
        },
        visibleInForm: catalog.keyword.isVisible,
        visibleInListing: catalog.keyword.isVisible,
        visibleInView: catalog.keyword.isVisible
      }
    }
  });
});

// Experts
adminResourceFactory.factory('experts', {
  title: 'Эксперты',
  jsonApi: '/api/v1/experts',
  stringify: 'name',
  meta: {
    icon: 'list'
  },
  properties: {
    name: {
      title: 'Имя',
      constraints: {
        notEmpty: validationConstraints.notEmpty()
      },
      errorMessages: {
        notEmpty: 'Имя не может быть пустым'
      },
      linkable: true
    },
    photoUri: {
      title: 'Ссылка на фото',
      constraints: {
        notEmpty: validationConstraints.notEmpty()
      },
      errorMessages: {
        notEmpty: 'Ссылка на фото не может быть пустым'
      },
      visibleInListing: false
    },
    description: {
      title: 'Описание',
      component: 'textarea',
      constraints: {
        notEmpty: validationConstraints.notEmpty()
      },
      errorMessages: {
        notEmpty: 'Описание не может быть пустым'
      },
      visibleInListing: false
    }
  }
});

// Opinions
adminResourceFactory.factory('opinions', {
  title: 'Мнения',
  jsonApi: '/api/v1/opinions',
  stringify: 'title',
  meta: {
    icon: 'list'
  },
  properties: {
    title: {
      title: 'Название',
      constraints: {
        notEmpty: validationConstraints.notEmpty()
      },
      errorMessages: {
        notEmpty: 'Название не может быть пустым'
      },
      linkable: true
    },
    content: {
      title: 'Содержание',
      component: 'ckeditor',
      constraints: {
        notEmpty: validationConstraints.notEmpty()
      },
      errorMessages: {
        notEmpty: 'Содержание не может быть пустым'
      },
      visibleInListing: false,
      visibleInView: false
    },
    viewsCount: {
      title: 'Просмотров',
      format: 'number',
      component: 'number-input',
      defaultValue: 0
    },
    isActive: {
      title: 'Активен',
      format: 'boolean',
      component: 'checkbox',
      defaultValue: true
    },
    createdAt: {
      title: 'Дата создания',
      format: 'date',
      component: 'date-time-picker',
      visibleInListing: false
    },
    expert: {
      title: 'Эксперт',
      format: 'many-to-one',
      component: 'many-to-one-select',
      constraints: {
        notEmpty: validationConstraints.notEmpty()
      },
      errorMessages: {
        notEmpty: 'Эксперт не может быть пустым'
      },
      dependency: 'experts'
    }
  }
});

// News
adminResourceFactory.factory('news', {
  title: 'Новости',
  jsonApi: '/api/v1/news',
  stringify: 'title',
  meta: {
    icon: 'list'
  },
  properties: {
    title: {
      title: 'Название',
      constraints: {
        notEmpty: validationConstraints.notEmpty()
      },
      errorMessages: {
        notEmpty: 'Название не может быть пустым'
      },
      linkable: true
    },
    content: {
      title: 'Содержание',
      component: 'ckeditor',
      constraints: {
        notEmpty: validationConstraints.notEmpty()
      },
      errorMessages: {
        notEmpty: 'Содержание не может быть пустым'
      },
      visibleInListing: false,
      visibleInView: false
    },
    isActive: {
      title: 'Активен',
      format: 'boolean',
      component: 'checkbox',
      defaultValue: true
    },
    createdAt: {
      title: 'Дата создания',
      format: 'date',
      component: 'date-time-picker',
      visibleInListing: false
    },
    category: {
      title: 'Категория',
      format: 'many-to-one',
      component: 'many-to-one-select',
      constraints: {
        notEmpty: validationConstraints.notEmpty()
      },
      errorMessages: {
        notEmpty: 'Категория не может быть пустым'
      },
      dependency: 'news-categories'
    }
  }
});

// Regions
adminResourceFactory.factory('regions', {
  title: 'Регионы',
  jsonApi: '/api/v1/regions',
  stringify: 'name',
  meta: {
    icon: 'list'
  },
  properties: {
    name: {
      title: 'Название',
      constraints: {
        notEmpty: validationConstraints.notEmpty()
      },
      errorMessages: {
        notEmpty: 'Название не может быть пустым'
      },
      linkable: true
    }
  }
});

// Users
adminResourceFactory.factory('users', {
  title: 'Пользователи',
  jsonApi: '/api/v1/users',
  stringify: 'email',
  meta: {
    icon: 'users'
  },
  properties: {
    email: {
      title: 'E-mail',
      constraints: {
        notEmpty: validationConstraints.notEmpty(),
        email: validationConstraints.email()
      },
      errorMessages: {
        notEmpty: 'E-mail не может быть пустым',
        email: 'Не правильный формат e-mail'
      },
      linkable: true
    },
    plainPassword: {
      title: 'Пароль',
      constraints: {
        notEmpty: validationConstraints.notEmpty()
      },
      errorMessages: {
        notEmpty: 'Пароль не может быть пустым'
      },
      visibleInListing: false,
      visibleInView: false,
      visibleInEditForm: false,
      component: 'password'
    },
    firstname: {
      title: 'Имя',
      constraints: {
        notEmpty: validationConstraints.notEmpty()
      },
      errorMessages: {
        notEmpty: 'Имя не может быть пустым'
      }
    },
    lastname: {
      title: 'Фамилия',
      constraints: {
        notEmpty: validationConstraints.notEmpty()
      },
      errorMessages: {
        notEmpty: 'Фамилия не может быть пустой'
      }
    },
    birthday: {
      title: 'Дата рождения',
      format: 'date',
      component: 'date-time-picker',
      constraints: {
        notEmpty: validationConstraints.notEmpty()
      },
      errorMessages: {
        notEmpty: 'Дата рождения не может быть пустой'
      }
    },
    isDoctor: {
      title: 'Доктор?',
      format: 'boolean',
      component: 'checkbox',
      visibleInView: false,
      visibleInListing: false,
      visibleInEditForm: false,
      defaultValue: false
    },
    specializationExperienceYears: {
      title: 'Стаж',
      format: 'number',
      component: 'number-input',
      visibleInListing: false
    },
    specializationGraduationDate: {
      title: 'Год окончания',
      format: 'date',
      component: 'date-time-picker',
      visibleInListing: false
    },
    specializationInstitutionAddress: {
      title: 'Адрес',
      visibleInListing: false
    },
    specializationInstitutionName: {
      title: 'Учебное заведение',
      visibleInListing: false
    },
    specializationInstitutionPhone: {
      title: 'Телефон',
      visibleInListing: false
    },
    specializationName: {
      title: 'Основная специализация',
      visibleInListing: false
    },
    isSubscribed: {
      title: 'Подписан на рассылку',
      format: 'boolean',
      component: 'checkbox',
      visibleInListing: false,
      visibleInCreateForm: true,
      visibleInView: false,
      defaultValue: true
    },
    // isEnabled: {
    //   title: 'Активен',
    //   format: 'boolean',
    //   component: 'checkbox',
    //   defaultValue: true
    // },
    region: {
      title: 'Регион',
      format: 'many-to-one',
      component: 'many-to-one-select',
      constraints: {
        notEmpty: validationConstraints.notEmpty()
      },
      errorMessages: {
        notEmpty: 'Регион не может быть пустым'
      },
      dependency: 'regions'
    }
  }
});

// Pages
adminResourceFactory.factory('pages', {
  title: 'Страницы',
  rest: {
    baseUrl: '/api/old/pages',
  },
  meta: {
    icon: 'file-text-o'
  },
  stringify: 'title',
  listingComponent: require("engage-360d-admin/components/page/PageTree"),
  formComponent: function(props) {
    props.pageBlockRegistry = pageBlockRegistry;
    return require("engage-360d-admin/components/page/PageForm")(props);
  },
  properties: {
    url: {
      title: 'Ссылка на страницу',
      constraints: {
        notEmpty: validationConstraints.notEmpty()
      },
      errorMessages: {
        notEmpty: 'Ссылка на страницу не может быть пустой'
      }
    },
    title: {
      title: 'Заголовок',
      constraints: {
        notEmpty: validationConstraints.notEmpty()
      },
      errorMessages: {
        notEmpty: 'Заголовок не может быть пустым'
      }
    },
    description: {
      title: 'Описание для SEO',
      constraints: {
        notEmpty: validationConstraints.notEmpty()
      },
      errorMessages: {
        notEmpty: 'Описание для SEO не может быть пустым'
      }
    },
    keywords: {
      title: 'Ключевые слова для SEO',
      constraints: {
        notEmpty: validationConstraints.notEmpty()
      },
      errorMessages: {
        notEmpty: 'Ключевые слова для SEO не может быть пустым'
      }
    },
    isActive: {
      title: 'Страница опубликована',
      component: 'checkbox',
      format: 'boolean'
    },
    pageBlocks: {
      visibleInView: false,
      defaultValue: []
    }
  }
});

// Rendering
$(function() {
  React.renderComponent(
    Admin({
      title: 'Такеда. Кардиомагнил',
      copyright: '© 2015 Такеда. Кардиомагнил',
      creatorName: 'Engage',
      creatorUrl: 'http://iengage.ru',
      signUpEmail: 'it@iengage.ru',
      adminSetup: adminSetup,
      menu: menu
    }),
    document.getElementById('Admin')
  );
});
