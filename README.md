# Новостной Портал 

https://www.ogirk.ru

Высокопроизводительный новостной портал с гибридной архитектурой, объединяющей WordPress CMS, NestJS API и Vue.js фронтенд для оптимального управления контентом и пользовательского опыта.

## 🏗️ Архитектура

```
Vue.js + Quasar ◄── NestJS + GraphQL ◄── WordPress CMS
     ▲                    ▲                    ▲
  Frontend           Fast API Layer      Content Management
```

**Решение проблемы производительности WordPress** через промежуточный NestJS слой с кэшированием и оптимизированными GraphQL запросами.

## 🚀 Технологический Стек

**Frontend:** Vue.js 2, Quasar Framework, Vuex, Apollo GraphQL, SSR  
**API:** NestJS, TypeScript, GraphQL, MongoDB, Mongoose, Real-time subscriptions  
**CMS:** WordPress, Custom Plugins, ACF, Custom Post Types  
**Search:** Elasticsearch для быстрого полнотекстового поиска  
**Cache:** Multi-level caching strategy

## 🎯 Ключевые Особенности

- **Мультиформатный контент**: Статьи, галереи, видео, интерактивные карты
- **Elasticsearch поиск**: Быстрый полнотекстовый поиск с фасетной фильтрацией
- **Real-time обновления**: GraphQL subscriptions для живого контента
- **Многоуровневое кэширование**: WordPress → NestJS → Frontend
- **Адаптивный дизайн**: Mobile-first с Quasar компонентами
- **SEO оптимизация**: SSR + мета-теги + структурированные данные

## 🔧 Технические Решения

**GraphQL API Design**
- Типобезопасные операции с DataLoader паттерном
- Real-time subscriptions для живых обновлений
- Оптимизированные запросы с field-level resolvers

**Database & Search**
- **MongoDB** с Mongoose ODM для основных данных
- **Elasticsearch** для полнотекстового поиска и аналитики
- Индексация контента с морфологическим анализом
- Агрегационные пайплайны для сложных запросов

**Performance Optimization**
- Синхронизация WordPress → NestJS с инкрементальными обновлениями
- Multi-level caching (Redis, Application, CDN)
- Code splitting и lazy loading
- Оптимизация изображений с responsive delivery

**WordPress Integration**
- Кастомные плагины с модульной архитектурой
- ACF для гибкого моделирования контента
- REST API расширения для синхронизации

---

*Этот проект демонстрирует продвинутые навыки full-stack разработки, показывая экспертизу в современных JavaScript фреймворках, дизайне API, оптимизации баз данных и масштабируемых архитектурных паттернах.*